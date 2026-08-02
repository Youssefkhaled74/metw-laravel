<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierAssignmentStatus;
use App\Models\CourierAssignment;

/**
 * Automatic timeout handling (Section 3): any offer that receives no response
 * within the configured working hours is automatically rejected, and once a
 * courier accepts, the remaining offers are limited to the response window.
 */
class CourierTimeoutProcessor
{
    public function __construct(
        protected CourierAssignmentService $assignmentService
    ) {}

    public function process(): int
    {
        $expired = 0;

        $overdue = CourierAssignment::query()
            ->with('assignable')
            ->pending()
            ->where(function ($query) {
                // No response within the working-hours deadline → auto reject.
                $query->whereNotNull('response_deadline_at')
                    ->where('response_deadline_at', '<=', now())

                    // No response before the window closes after the first acceptance.
                    ->orWhere(function ($window) {
                        $window->whereNotNull('window_closes_at')
                            ->where('window_closes_at', '<=', now());
                    });
            })
            ->get();

        foreach ($overdue as $assignment) {
            $this->assignmentService->expire($assignment, 'auto_reject_timeout');
            $expired++;
        }

        return $expired;
    }
}
