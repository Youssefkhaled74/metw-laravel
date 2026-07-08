<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function incoming(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $availability = $this->metwGoCourierService->availabilityStatus($representative);

            if ($availability !== 'online') {
                return responseJson(false, 'الرجاء التفعيل على الإنترنت لعرض الطلبات.', [
                    'status' => 'offline',
                ], 403);
            }

            $limit = (int) $request->input('limit', 10);
            $page = (int) $request->input('page', 1);

            $orders = $this->metwGoCourierService->incomingOrdersQuery($representative)
                ->paginate($limit, ['*'], 'page', $page);

            $data = collect($orders->items())
                ->map(fn ($order) => $this->metwGoCourierService->formatOrderItem($order))
                ->values();

            return responseJson(true, '', [
                'data' => $data,
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'has_more' => $orders->hasMorePages(),
                    'total' => $orders->total(),
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            return responseJson(false, 'لا يمكن عرض الطلبات قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show($orderId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $orderItem = OrderItem::with([
                'order',
                'package.packageDetails',
                'package.pickupAddress',
                'package.dropoffAddress',
                'route',
            ])->findOrFail($orderId);

            $details = $this->metwGoCourierService->formatOrderDetails($orderItem);

            return responseJson(true, '', $details, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'Order not found.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function start($orderId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'accepted_fee' => ['nullable', 'numeric', 'min:0'],
            ]);

            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $availability = $this->metwGoCourierService->availabilityStatus($representative);

            if ($availability !== 'online') {
                return responseJson(false, 'لا يمكن بدء الطلب وأنت غير متصل.', [
                    'status' => 'offline',
                ], 403);
            }

            $hasActive = $this->metwGoCourierService->activeOrderQuery($representative)->exists();

            if ($hasActive) {
                return responseJson(false, 'You already have an active order.', null, 409);
            }

            $orderItem = OrderItem::where('id', $orderId)
                ->whereNull('representative_id')
                ->where('status', 'pending')
                ->firstOrFail();

            $orderItem->update([
                'representative_id' => $representative->id,
                'accepted_fee' => $validated['accepted_fee'] ?? $orderItem->est_price,
                'accepted_at' => now(),
                'status' => 'accepted',
            ]);

            $metadata = $orderItem->order?->metadata ?? [];
            $metadata['active_courier_id'] = $representative->id;
            $orderItem->order?->update(['metadata' => $metadata]);

            return responseJson(true, 'تم بدء الطلب', [
                'active_order' => [
                    'id' => $orderItem->id,
                    'order_number' => $orderItem->order?->order_number ?? ('MET-' . $orderItem->id),
                    'status' => 'accepted',
                    'label' => 'طلب جاري',
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (str_contains($e->getMessage(), 'OrderItem')) {
                return responseJson(false, 'This order has already been taken by another courier.', null, 409);
            }
            return responseJson(false, 'Order not found.', null, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');
            return responseJson(false, 'لا يمكن بدء الطلب قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function active(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $activeOrder = $this->metwGoCourierService->activeOrderQuery($representative)
                ->first();

            if (!$activeOrder) {
                return responseJson(true, '', ['active_order' => null], 200);
            }

            return responseJson(true, '', [
                'active_order' => $this->metwGoCourierService->formatOrderItem($activeOrder),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
