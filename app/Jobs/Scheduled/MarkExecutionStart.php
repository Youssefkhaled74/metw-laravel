<?php

namespace App\Jobs\Scheduled;

use App\Contracts\CronJob;
use App\Enum\AdvancePaymentStatus;
use App\Enum\ShipmentRequestStatus;
use App\Models\ShipmentRequest;
use App\Services\CourierSystem\CourierRequestWorkflowService;
use App\Services\CourierSystem\CourierSystemConfigService;
use App\Services\Cron\CronJobResult;

/**
 * Cron job 5 (W real hours): safety net that starts the execution of
 * inter-governorate requests whose advance payment was already confirmed but
 * whose execution never started (e.g. the confirmation hook was missed).
 * It only ever starts paid requests and never unpaid ones.
 */
class MarkExecutionStart implements CronJob
{
    public function __construct(
        protected CourierRequestWorkflowService $workflow,
        protected CourierSystemConfigService $config
    ) {}

    public function name(): string
    {
        return 'shipment-requests-mark-execution-start';
    }

    public function handle(): CronJobResult
    {
        $hours = $this->config->executionStartHours();
        $cutoff = now()->subHours($hours);

        $candidates = ShipmentRequest::query()
            ->with([
                'selectedPath',
                'advancePayments',
                'senderContact.primaryAddress',
                'receiverContact.primaryAddress',
            ])
            ->where('status', ShipmentRequestStatus::AWAITING_ADVANCE->value)
            ->whereNotNull('selected_request_path_id')
            ->whereNull('execution_started_at')
            ->whereHas('advancePayments', fn ($query) => $query->where('status', AdvancePaymentStatus::CONFIRMED->value))
            ->where(function ($query) use ($cutoff) {
                $query->whereHas('advancePayments', fn ($payment) => $payment
                    ->where('status', AdvancePaymentStatus::CONFIRMED->value)
                    ->where('confirmed_at', '<=', $cutoff))
                    ->orWhere(function ($fallback) use ($cutoff) {
                        $fallback->whereDoesntHave('advancePayments', fn ($payment) => $payment->whereNotNull('confirmed_at'))
                            ->where('submitted_at', '<=', $cutoff);
                    });
            })
            ->get();

        $interGovernorate = $candidates->filter(function (ShipmentRequest $request) {
            return $this->isInterGovernorate($request);
        });

        $started = 0;
        $startedIds = [];
        $failedIds = [];

        foreach ($interGovernorate as $request) {
            try {
                $this->workflow->startExecution($request);
                $started++;
                $startedIds[] = $request->id;
            } catch (\Throwable $throwable) {
                report($throwable);
                $failedIds[] = $request->id;
            }
        }

        return new CronJobResult($interGovernorate->count(), $started, [
            'timeout_hours' => $hours,
            'started_request_ids' => $startedIds,
            'failed_request_ids' => $failedIds,
            'non_inter_governorate_skipped' => $candidates->count() - $interGovernorate->count(),
        ]);
    }

    protected function isInterGovernorate(ShipmentRequest $request): bool
    {
        $origin = $request->senderContact?->primaryAddress?->governorate_id;
        $destination = $request->receiverContact?->primaryAddress?->governorate_id;

        return $origin !== null && $destination !== null && (int) $origin !== (int) $destination;
    }
}
