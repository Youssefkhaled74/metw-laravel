<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\OrderStatus;
use App\Enum\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MetwGo\CreateReturnRequest;
use App\Models\Order;
use App\Models\ReturnReason;
use App\Services\MetwGo\MetwGoCourierService;
use App\Services\MetwGo\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepReturnController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService,
        protected ReturnService $returnService,
    ) {}

    public function create(CreateReturnRequest $request, int $orderId): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);
            $this->metwGoCourierService->assertApproved($representative);

            $order = Order::with('user')->findOrFail($orderId);

            $orderItem = $order->orderItems()
                ->where('representative_id', $representative->id)
                ->where('status', OrderStatus::DELIVERED->value)
                ->first();

            if (!$orderItem) {
                return responseJson(false, 'لا يمكن إنشاء طلب إرجاع لهذا الطلب. الطلب لم يتم تسليمه أو غير تابع لك.', null, 422);
            }

            $validated = $request->validated();
            $status = ReturnRequestStatus::tryFrom($validated['status']);

            $returnRequest = $this->returnService->createReturnRequest(
                order: $order,
                representative: $representative,
                user: $order->user,
                reasonId: (int) $validated['reason_id'],
                customReasonText: $validated['custom_reason_text'] ?? null,
                status: $status,
                rejectionReason: $validated['rejection_reason'] ?? null,
            );

            $data = $this->returnService->formatReturnRequest($returnRequest);

            if ($returnRequest->whatsapp_deep_link) {
                $data['whatsapp_deep_link'] = $returnRequest->whatsapp_deep_link;
            }

            return responseJson(true, 'تم إنشاء طلب الإرجاع بنجاح', $data, 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'الطلب غير موجود.', null, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function reasons(): JsonResponse
    {
        try {
            $reasons = ReturnReason::withoutGlobalScope('active')
                ->where('is_active', true)
                ->get(['id', 'reason_text', 'is_active']);

            return responseJson(true, '', [
                'reasons' => $reasons,
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
