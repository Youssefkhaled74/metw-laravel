<?php

namespace App\Jobs\Scheduled;

use App\Contracts\CronJob;
use App\Enum\AdvancePaymentStatus;
use App\Enum\CourierAssignmentStatus;
use App\Enum\ShipmentRequestStatus;
use App\Enum\SubmitterType;
use App\Models\ShipmentRequest;
use App\Services\CourierSystem\CourierSystemConfigService;
use App\Services\Cron\CronJobResult;

/**
 * Cron job 3 (Z real hours): a User request that stays in awaiting-advance
 * without submitting the advance payment for Z real hours is cancelled along
 * with any pending advance payment records. Seller requests are never touched.
 */
class CancelUnpaidAdvancePayments implements CronJob
{
    public function __construct(
        protected CourierSystemConfigService $config
    ) {}

    public function name(): string
    {
        return 'advance-payments-cancel-unpaid';
    }

    public function handle(): CronJobResult
    {
        $hours = $this->config->cancelUnpaidAdvanceHours();
        $cutoff = now()->subHours($hours);

        $requests = ShipmentRequest::query()
            ->with(['selectedPath', 'advancePayments'])
            ->where('status', ShipmentRequestStatus::AWAITING_ADVANCE->value)
            ->where('submitter_type', SubmitterType::USER->value)
            ->where(function ($query) use ($cutoff) {
                $query->whereHas('selectedPath', fn ($path) => $path->where('client_selected_at', '<=', $cutoff))
                    ->orWhere(function ($fallback) use ($cutoff) {
                        $fallback->whereDoesntHave('selectedPath', fn ($path) => $path->whereNotNull('client_selected_at'))
                            ->where('created_at', '<=', $cutoff);
                    });
            })
            ->get();

        $cancelled = 0;
        $cancelledRequestIds = [];
        $rejectedPaymentIds = [];

        foreach ($requests as $request) {
            try {
                foreach ($request->advancePayments as $payment) {
                    if (($payment->status?->value ?? $payment->status) === AdvancePaymentStatus::PENDING->value) {
                        $payment->update([
                            'status' => AdvancePaymentStatus::REJECTED->value,
                            'rejected_at' => now(),
                            'notes' => 'Auto-cancelled: advance unpaid after ' . $hours . ' hours.',
                            'metadata' => array_merge((array) $payment->metadata, [
                                'auto_cancel_reason' => 'advance_payment_timeout',
                                'auto_cancel_at' => now()->toDateTimeString(),
                            ]),
                        ]);
                        $rejectedPaymentIds[] = $payment->id;
                    }
                }

                $request->assignments()
                    ->where('status', CourierAssignmentStatus::PENDING->value)
                    ->update(['status' => CourierAssignmentStatus::CANCELLED->value]);

                $request->update([
                    'status' => ShipmentRequestStatus::CANCELLED->value,
                    'closed_at' => now(),
                    'metadata' => array_merge((array) $request->metadata, [
                        'auto_cancel_reason' => 'advance_payment_timeout',
                        'auto_cancel_at' => now()->toDateTimeString(),
                        'auto_cancel_after_hours' => $hours,
                    ]),
                ]);

                $cancelled++;
                $cancelledRequestIds[] = $request->id;
            } catch (\Throwable $throwable) {
                report($throwable);
            }
        }

        return new CronJobResult($requests->count(), $cancelled, [
            'timeout_hours' => $hours,
            'cancelled_request_ids' => $cancelledRequestIds,
            'rejected_payment_ids' => $rejectedPaymentIds,
        ]);
    }
}
