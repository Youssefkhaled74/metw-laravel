<?php

namespace App\Jobs\Scheduled;

use App\Contracts\CronJob;
use App\Models\CourierAssignment;
use App\Services\CourierSystem\CourierAssignmentService;
use App\Services\Cron\CronJobResult;

/**
 * Cron job 1 (X working hours): automatically rejects every courier offer
 * whose response deadline has passed without a response. Only working hours
 * count toward the deadline (deadline is stored on the assignment itself).
 */
class AutoRejectOverdueOffers implements CronJob
{
    public function __construct(
        protected CourierAssignmentService $assignmentService
    ) {}

    public function name(): string
    {
        return 'courier-auto-reject';
    }

    public function handle(): CronJobResult
    {
        $overdue = CourierAssignment::query()
            ->with('assignable')
            ->pending()
            ->whereNotNull('response_deadline_at')
            ->where('response_deadline_at', '<=', now())
            ->get();

        $affected = 0;
        $expiredIds = [];

        foreach ($overdue as $assignment) {
            try {
                $this->assignmentService->expire($assignment, 'auto_reject_timeout');
                $affected++;
                $expiredIds[] = $assignment->id;
            } catch (\Throwable $throwable) {
                report($throwable);
            }
        }

        return new CronJobResult($overdue->count(), $affected, [
            'expired_assignment_ids' => $expiredIds,
        ]);
    }
}
