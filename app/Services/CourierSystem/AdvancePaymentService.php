<?php

namespace App\Services\CourierSystem;

use App\Enum\AdvancePaymentStatus;
use App\Enum\ShipmentRequestStatus;
use App\Enum\SubmitterType;
use App\Models\AdvancePayment;
use App\Models\RequestPath;
use App\Models\ShipmentRequest;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Validation\ValidationException;

/**
 * Advance payment flow (Section 3, Common Rules):
 *  - a User submits the advance (with WhatsApp messages) then Admin confirms it;
 *  - a Seller's approval of the path is enough and needs no payment.
 */
class AdvancePaymentService
{
    public function __construct(
        protected CourierRequestWorkflowService $workflow
    ) {}

    public function submitForUser(ShipmentRequest $request, User $user, array $data): AdvancePayment
    {
        $this->assertAwaitingAdvance($request);
        $this->assertOwnedByUser($request, $user);

        $path = $this->selectedPathOrFail($request);

        return $this->createPayment($request, $path, SubmitterType::USER, $user->id, $data);
    }

    public function submitForSeller(ShipmentRequest $request, Vendor $vendor, array $data): AdvancePayment
    {
        $this->assertAwaitingAdvance($request);
        $this->assertOwnedBySeller($request, $vendor);

        $path = $this->selectedPathOrFail($request);

        return $this->createPayment($request, $path, SubmitterType::SELLER, $vendor->id, $data);
    }

    public function confirm(AdvancePayment $payment, ?int $adminId = null): AdvancePayment
    {
        if (($payment->status?->value ?? $payment->status) !== AdvancePaymentStatus::PAID->value) {
            throw ValidationException::withMessages([
                'advance_payment' => ['Only paid advance payments can be confirmed.'],
            ]);
        }

        $payment->update([
            'status' => AdvancePaymentStatus::CONFIRMED->value,
            'confirmed_by' => $adminId,
            'confirmed_at' => now(),
        ]);

        $request = $payment->payable;

        if ($request instanceof ShipmentRequest
            && ($request->status?->value ?? $request->status) === ShipmentRequestStatus::AWAITING_ADVANCE->value) {
            $this->workflow->startExecution($request);
        }

        return $payment->fresh(['requestPath', 'submitter', 'confirmer']);
    }

    public function reject(AdvancePayment $payment, ?int $adminId = null, ?string $note = null): AdvancePayment
    {
        if (($payment->status?->value ?? $payment->status) !== AdvancePaymentStatus::PAID->value) {
            throw ValidationException::withMessages([
                'advance_payment' => ['Only paid advance payments can be rejected.'],
            ]);
        }

        $payment->update([
            'status' => AdvancePaymentStatus::REJECTED->value,
            'confirmed_by' => $adminId,
            'rejected_at' => now(),
            'notes' => $note ?: $payment->notes,
        ]);

        return $payment->fresh();
    }

    protected function createPayment(ShipmentRequest $request, RequestPath $path, SubmitterType $type, int $submitterId, array $data): AdvancePayment
    {
        $amount = (float) ($data['amount'] ?? $path->total_cost);

        $payment = $request->advancePayments()->create([
            'request_path_id' => $path->id,
            'submitter_type' => $type->value,
            'submitter_id' => $submitterId,
            'amount' => $amount,
            'currency' => $data['currency'] ?? 'EGP',
            'status' => AdvancePaymentStatus::PAID->value,
            'payment_method' => $data['payment_method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        event(new \App\Events\AdvancePaymentSubmitted($payment));

        return $payment->fresh(['requestPath', 'submitter']);
    }

    protected function selectedPathOrFail(ShipmentRequest $request): RequestPath
    {
        $path = $request->selectedPath;

        if (! $path) {
            throw ValidationException::withMessages([
                'request_path' => ['No path has been selected for this request yet.'],
            ]);
        }

        return $path;
    }

    protected function assertAwaitingAdvance(ShipmentRequest $request): void
    {
        if (($request->status?->value ?? $request->status) !== ShipmentRequestStatus::AWAITING_ADVANCE->value) {
            throw ValidationException::withMessages([
                'shipment_request' => ['This request is not awaiting an advance payment.'],
            ]);
        }
    }

    protected function assertOwnedByUser(ShipmentRequest $request, User $user): void
    {
        if ((int) $request->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'shipment_request' => ['This request does not belong to you.'],
            ]);
        }
    }

    protected function assertOwnedBySeller(ShipmentRequest $request, Vendor $vendor): void
    {
        if ($this->workflow->isSellerRequest($request)
            && (int) $request->submitter_id !== (int) $vendor->id) {
            throw ValidationException::withMessages([
                'shipment_request' => ['This request does not belong to your store.'],
            ]);
        }
    }
}
