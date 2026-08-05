<?php

namespace App\Jobs\Scheduled;

use App\Contracts\CronJob;
use App\Models\CourierAssignment;
use App\Services\CourierSystem\CourierAssignmentService;
use App\Services\Cron\CronJobResult;

/**
 * Cron job 2 (Y working hours): after the first courier accepts a request,
 * the remaining offers get a short response window. This job expires every
 * offer whose window has closed without a response.
 */
class CloseExpiredResponseWindows implements CronJob
{
    public function __construct(
        protected CourierAssignmentService $assignmentService
    ) {}

    public function name(): string
    {
        return 'courier-close-response-window';
    }

    public function handle(): CronJobResult
    {
        $expired = CourierAssignment::query()
            ->with('assignable')
            ->pending()
            ->whereNotNull('window_closes_at')
            ->where('window_closes_at', '<=', now())
            ->get();

        $affected = 0;
        $expiredIds = [];

        foreach ($expired as $assignment) {
            try {
                $this->assignmentService->expire($assignment, 'response_window_closed');
                $affected++;
                $expiredIds[] = $assignment->id;
            } catch (\Throwable $throwable) {
                report($throwable);
            }
        }

        return new CronJobResult($expired->count(), $affected, [
            'expired_assignment_ids' => $expiredIds,
        ]);
    }
}
