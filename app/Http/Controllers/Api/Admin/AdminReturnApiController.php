<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enum\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderReturnRequest;
use App\Services\MetwGo\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminReturnApiController extends Controller
{
    public function __construct(
        protected ReturnService $returnService,
    ) {}

    public function pendingReturns(): JsonResponse
    {
        try {
            $returns = $this->returnService->pendingReturnsQuery()->get();

            return responseJson(true, '', [
                'returns' => $returns->map(fn ($r) => $this->returnService->formatReturnRequest($r)),
                'total' => $returns->count(),
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function complaints(): JsonResponse
    {
        try {
            $returns = $this->returnService->complaintReturnsQuery()->get();

            return responseJson(true, '', [
                'returns' => $returns->map(function ($r) {
                    $data = $this->returnService->formatReturnRequest($r);
                    $data['complaints'] = $r->complaints->map(fn ($c) => [
                        'id' => $c->id,
                        'complaint_reason' => $c->complaint_reason,
                        'admin_action' => $c->admin_action,
                        'action_reason' => $c->action_reason,
                        'created_at' => $c->created_at?->toIso8601String(),
                    ]);
                    return $data;
                }),
                'total' => $returns->count(),
            ], 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show(OrderReturnRequest $orderReturnRequest): JsonResponse
    {
        try {
            $orderReturnRequest->load(['order.orderItems', 'representative.user', 'user', 'reason', 'complaints.user', 'logs']);

            $data = $this->returnService->formatReturnRequest($orderReturnRequest);
            $data['logs'] = $orderReturnRequest->logs->map(fn ($log) => [
                'old_status' => $log->old_status,
                'new_status' => $log->new_status,
                'notes' => $log->notes,
                'created_at' => $log->created_at?->toIso8601String(),
            ]);

            return responseJson(true, '', $data, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function updateStatus(Request $request, OrderReturnRequest $orderReturnRequest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'in:completed,approved'],
                'admin_notes' => ['nullable', 'string', 'max:1000'],
            ]);

            if ($validated['status'] === 'completed') {
                $refund = $this->returnService->calculateRefund($orderReturnRequest->order);

                $orderReturnRequest->update([
                    'refund_amount' => $refund['refund_amount'],
                    'shipping_fees' => $refund['shipping_fees'],
                    'return_fees' => $refund['return_fees'],
                    'net_refund' => $refund['net_refund'],
                ]);

                $this->returnService->creditRefundToWallet($orderReturnRequest);
            }

            $newStatus = ReturnRequestStatus::tryFrom($validated['status']);
            $this->returnService->updateStatus($orderReturnRequest, $newStatus, $validated['admin_notes'] ?? null);

            return responseJson(true, 'تم تحديث حالة طلب الإرجاع بنجاح', [
                'return' => $this->returnService->formatReturnRequest($orderReturnRequest->fresh()),
            ], 200);
        } catch (ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function reactivate(Request $request, OrderReturnRequest $orderReturnRequest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'action_reason' => ['required', 'string', 'max:1000'],
            ]);

            $lastComplaint = $orderReturnRequest->complaints()->latest()->first();
            if ($lastComplaint) {
                $lastComplaint->update([
                    'admin_action' => 'reactivated',
                    'action_reason' => $validated['action_reason'],
                ]);
            }

            $this->returnService->updateStatus(
                $orderReturnRequest,
                ReturnRequestStatus::APPROVED,
                'إعادة تفعيل: ' . $validated['action_reason'],
            );

            return responseJson(true, 'تم إعادة تفعيل طلب الإرجاع وإشعار العميل بنجاح', [
                'return' => $this->returnService->formatReturnRequest($orderReturnRequest->fresh()),
            ], 200);
        } catch (ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
