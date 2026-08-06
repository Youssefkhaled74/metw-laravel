<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\CourierAssignmentStatus;
use App\Enum\OrderStatus;
use App\Enum\RequestLegType;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\RejectionReason;
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
            $this->metwGoCourierService->assertApproved($representative);

            $orderItem = $this->metwGoCourierService->findOrderForRepresentative($representative, (int) $orderId);

            return responseJson(true, '', $this->metwGoCourierService->formatOrderDetails($orderItem), 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');

            return responseJson(false, 'لا يمكن عرض تفاصيل الطلب قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'الطلب غير متاح لهذا المندوب.', null, 404);
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

            if ($this->metwGoCourierService->activeOrderQuery($representative)->exists()) {
                return responseJson(false, 'You already have an active order.', null, 409);
            }

            $orderItem = DB::transaction(function () use ($representative, $orderId, $validated) {
                $eligibleOrder = $this->metwGoCourierService->incomingOrdersQuery($representative)
                    ->whereKey((int) $orderId)
                    ->firstOrFail();

                $orderItem = OrderItem::query()
                    ->whereKey($eligibleOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($orderItem->representative_id !== null || $orderItem->status !== 'pending') {
                    throw new \RuntimeException('order_unavailable');
                }

                $orderItem->update([
                    'representative_id' => $representative->id,
                    'accepted_fee' => $validated['accepted_fee'] ?? $orderItem->est_price,
                    'accepted_at' => now(),
                    'status' => 'accepted',
                ]);

                $orderItem->assignments()->updateOrCreate(
                    ['representative_id' => $representative->id],
                    [
                        'leg_type' => RequestLegType::DIRECT_DELIVERY->value,
                        'status' => CourierAssignmentStatus::ACCEPTED->value,
                        'offered_at' => now(),
                        'responded_at' => now(),
                        'rejection_reason_id' => null,
                        'rejection_note' => null,
                    ]
                );

                $metadata = $orderItem->order?->metadata ?? [];
                $metadata['active_courier_id'] = $representative->id;
                $orderItem->order?->update(['metadata' => $metadata]);

                return $orderItem->fresh(['order']);
            });

            return responseJson(true, 'تم بدء الطلب', [
                'active_order' => [
                    'id' => $orderItem->id,
                    'order_number' => $orderItem->order?->order_number ?? ('MET-' . $orderItem->id),
                    'status' => 'accepted',
                    'label' => 'طلب جاري',
                ],
            ], 200);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'order_unavailable') {
                return responseJson(false, 'This order has already been taken by another courier.', null, 409);
            }

            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'الطلب غير متاح لهذا المندوب.', null, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');

            return responseJson(false, 'لا يمكن بدء الطلب قبل الموافقة على الحساب.', [
                'status' => $status,
            ], 403);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function reject($orderId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $validated = $request->validate([
                'reason_id' => ['required', 'integer', 'exists:rejection_reasons,id'],
                'custom_reason' => ['nullable', 'string', 'max:500'],
            ]);

            $reason = RejectionReason::findOrFail($validated['reason_id']);

            $orderItem = DB::transaction(function () use ($representative, $orderId, $validated) {
                $eligibleOrder = $this->metwGoCourierService->incomingOrdersQuery($representative)
                    ->whereKey((int) $orderId)
                    ->firstOrFail();

                $orderItem = OrderItem::query()
                    ->whereKey($eligibleOrder->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($orderItem->representative_id !== null || $orderItem->status !== 'pending') {
                    throw new \RuntimeException('order_unavailable');
                }

                $orderItem->assignments()->updateOrCreate(
                    ['representative_id' => $representative->id],
                    [
                        'leg_type' => RequestLegType::DIRECT_DELIVERY->value,
                        'status' => CourierAssignmentStatus::REJECTED->value,
                        'offered_at' => now(),
                        'responded_at' => now(),
                        'rejection_reason_id' => $validated['reason_id'],
                        'rejection_note' => $validated['custom_reason'] ?? null,
                    ]
                );

                return $orderItem;
            });

            return responseJson(true, 'تم رفض الطلب', [
                'order_id' => (int) $orderItem->id,
                'status' => OrderStatus::REJECTED->value,
                'order_status' => $orderItem->status,
                'reason' => $reason->reason_text,
                'custom_reason' => $validated['custom_reason'] ?? null,
                'removed_from_queue' => true,
            ], 200);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'order_unavailable') {
                return responseJson(false, 'لم يتم العثور على الطلب أو تم أخذه بواسطة مندوب آخر.', null, 409);
            }

            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (str_contains($e->getMessage(), 'RejectionReason')) {
                return responseJson(false, 'سبب الرفض غير موجود.', null, 404);
            }

            return responseJson(false, 'الطلب غير متاح لهذا المندوب.', null, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $status = $e->validator->errors()->first('approval_status');

            if ($status) {
                return responseJson(false, 'لا يمكن رفض الطلب قبل الموافقة على الحساب.', [
                    'status' => $status,
                ], 403);
            }

            throw $e;
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function active(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $activeOrder = $this->metwGoCourierService->activeOrderQuery($representative)->first();

            if (! $activeOrder) {
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
