<?php

namespace App\Services\MetwGo;

use App\Enum\ReturnRequestStatus;
use App\Models\Order;
use App\Models\OrderReturnRequest;
use App\Models\Representative;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    public function createReturnRequest(
        Order $order,
        Representative $representative,
        User $user,
        int $reasonId,
        ?string $customReasonText,
        ReturnRequestStatus $status,
        ?string $rejectionReason = null,
    ): OrderReturnRequest {
        $data = [
            'order_id' => $order->id,
            'representative_id' => $representative->id,
            'user_id' => $user->id,
            'reason_id' => $reasonId,
            'custom_reason_text' => $customReasonText,
            'status' => $status->value,
        ];

        if (in_array($status->value, [
            ReturnRequestStatus::REJECTED->value,
            ReturnRequestStatus::REJECTED_RETURN->value,
        ])) {
            $data['rejection_reason'] = $rejectionReason;
            $data['whatsapp_deep_link'] = $this->generateWhatsAppLink($user, $rejectionReason);
        }

        $returnRequest = DB::transaction(function () use ($data, $representative) {
            $returnRequest = OrderReturnRequest::create($data);

            $this->logStatusChange($returnRequest, null, $data['status'], Representative::class, $representative->id);

            return $returnRequest;
        });

        $this->notifyStatusChange($returnRequest, $user);

        return $returnRequest->fresh(['reason', 'order', 'representative.user']);
    }

    public function pendingReturnsQuery(): Builder
    {
        return OrderReturnRequest::query()
            ->with(['order', 'representative.user', 'user', 'reason'])
            ->where('status', ReturnRequestStatus::APPROVED->value)
            ->latest();
    }

    public function complaintReturnsQuery(): Builder
    {
        return OrderReturnRequest::query()
            ->with(['order', 'representative.user', 'user', 'reason', 'complaints'])
            ->whereIn('status', [
                ReturnRequestStatus::REJECTED->value,
                ReturnRequestStatus::REJECTED_RETURN->value,
            ])
            ->whereHas('complaints')
            ->latest();
    }

    public function updateStatus(OrderReturnRequest $returnRequest, ReturnRequestStatus $newStatus, ?string $notes = null, ?string $changedByType = null, ?int $changedById = null): OrderReturnRequest
    {
        $oldStatus = $returnRequest->status->value;

        $data = ['status' => $newStatus->value];

        if ($newStatus === ReturnRequestStatus::COMPLETED) {
            $data['completed_at'] = now();
        }

        DB::transaction(function () use ($returnRequest, $data, $oldStatus, $changedByType, $changedById, $notes) {
            $returnRequest->update($data);
            $this->logStatusChange($returnRequest, $oldStatus, $data['status'], $changedByType, $changedById, $notes);
        });

        $returnRequest->refresh();
        $this->notifyStatusChange($returnRequest, $returnRequest->user);

        return $returnRequest;
    }

    public function calculateRefund(Order $order): array
    {
        $totalPrice = (float) ($order->total_price ?? 0);
        $shippingFees = (float) ($order->final_price ?? 0) - $totalPrice;
        if ($shippingFees < 0) {
            $shippingFees = 0;
        }

        $returnFees = $totalPrice * 0.05;
        $returnFees = min($returnFees, 50);

        $netRefund = $totalPrice - $shippingFees - $returnFees;
        if ($netRefund < 0) {
            $netRefund = 0;
        }

        return [
            'refund_amount' => $totalPrice,
            'shipping_fees' => $shippingFees,
            'return_fees' => $returnFees,
            'net_refund' => $netRefund,
        ];
    }

    public function creditRefundToWallet(OrderReturnRequest $returnRequest): void
    {
        $user = $returnRequest->user;
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = \App\Models\Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'EGP',
                'is_active' => true,
            ]);
        }

        $wallet->increment('balance', $returnRequest->net_refund);

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'reference_id' => $returnRequest->id,
            'amount' => $returnRequest->net_refund,
            'description' => 'مردود طلب إرجاع #' . $returnRequest->id,
            'type' => 'increase',
        ]);
    }

    public function generateWhatsAppLink(User $user, ?string $reason): string
    {
        $phone = $user->phone;
        $phone = preg_replace('/^0/', '2', $phone);
        if (!str_starts_with($phone, '2')) {
            $phone = '2' . $phone;
        }

        $message = 'عذراً، تم رفض طلب الإرجاع الخاص بك.';
        if ($reason) {
            $message .= ' السبب: ' . $reason;
        }
        $message .= ' للتواصل معنا';

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    public function formatReturnRequest(OrderReturnRequest $returnRequest): array
    {
        return [
            'id' => $returnRequest->id,
            'order_number' => $returnRequest->order?->order_number,
            'customer_name' => $returnRequest->user->name ?? $returnRequest->user->username,
            'customer_phone' => $returnRequest->user->phone,
            'representative_name' => $returnRequest->representative?->first_name . ' ' . $returnRequest->representative?->last_name,
            'reason' => $returnRequest->reason?->reason_text ?? $returnRequest->custom_reason_text,
            'status' => $returnRequest->status?->value ?? $returnRequest->status,
            'rejection_reason' => $returnRequest->rejection_reason,
            'whatsapp_link' => $returnRequest->whatsapp_deep_link,
            'refund_amount' => (float) $returnRequest->refund_amount,
            'shipping_fees' => (float) $returnRequest->shipping_fees,
            'return_fees' => (float) $returnRequest->return_fees,
            'net_refund' => (float) $returnRequest->net_refund,
            'created_at' => $returnRequest->created_at?->toIso8601String(),
            'completed_at' => $returnRequest->completed_at?->toIso8601String(),
        ];
    }

    public function logStatusChange(OrderReturnRequest $returnRequest, ?string $oldStatus, string $newStatus, ?string $changedByType = null, ?int $changedById = null, ?string $notes = null): void
    {
        $returnRequest->logs()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_type' => $changedByType,
            'changed_by_id' => $changedById,
            'notes' => $notes,
        ]);
    }

    public function notifyStatusChange(OrderReturnRequest $returnRequest, User $user): void
    {
        $statusMessages = [
            ReturnRequestStatus::APPROVED->value => 'تمت الموافقة على طلب الإرجاع الخاص بك. سيتم ترتيب الشحن.',
            ReturnRequestStatus::REJECTED->value => 'تم رفض طلب الإرجاع الخاص بك.',
            ReturnRequestStatus::REJECTED_RETURN->value => 'تم رفض الإرجاع بعد الفحص.',
            ReturnRequestStatus::COMPLETED->value => 'تمت الموافقة على إرجاع المنتج، وسوف يتم رد قيمة المنتج لحسابك.',
        ];

        $message = $statusMessages[$returnRequest->status->value] ?? 'تم تحديث حالة طلب الإرجاع.';

        try {
            $user->notify(new OrderStatusUpdated(
                title: 'حالة طلب الإرجاع',
                body: $message,
                data: [
                    'key' => 'return_request_updated',
                    'return_id' => $returnRequest->id,
                    'order_number' => $returnRequest->order?->order_number,
                    'status' => $returnRequest->status->value,
                    'notification_type' => 'return',
                    'navigation_type' => 'return_details',
                ],
                type: 'return',
                navigationType: 'return_details'
            ));
        } catch (\Throwable $e) {
            // notification failure shouldn't break the flow
        }
    }
}
