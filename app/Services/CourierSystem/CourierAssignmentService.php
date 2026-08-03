<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierAssignmentStatus;
use App\Enum\DispatchState;
use App\Models\CourierAssignment;
use App\Models\RejectionReason;
use App\Models\Representative;
use Illuminate\Validation\ValidationException;

/**
 * Couriers' accept / reject behaviour for request offers (Section 2).
 */
class CourierAssignmentService
{
    public function __construct(
        protected CourierSystemConfigService $config,
        protected WorkingHoursService $workingHours,
        protected CourierDispatchService $dispatchService,
        protected CourierRequestWorkflowService $workflow
    ) {}

    public function accept(CourierAssignment $assignment, Representative $representative, ?float $acceptedFee = null): CourierAssignment
    {
        $this->assertOwnedBy($assignment, $representative);
        $this->assertRespondable($assignment);

        $assignable = $assignment->assignable;

        return \DB::transaction(function () use ($assignment, $assignable, $acceptedFee) {
            // Another courier already took the request before this courier responded.
            if ($assignable->representative_id !== null
                && $this->assignableExecutionStarted($assignable)
            ) {
                $assignment->update([
                    'status' => CourierAssignmentStatus::EXPIRED->value,
                    'responded_at' => now(),
                ]);

                throw ValidationException::withMessages([
                    'courier_assignment' => ['This request has already been accepted by another courier.'],
                ]);
            }

            $assignment->update([
                'status' => CourierAssignmentStatus::ACCEPTED->value,
                'responded_at' => now(),
                'metadata' => array_merge((array) $assignment->metadata, [
                    'accepted_fee' => $acceptedFee,
                ]),
            ]);

            $this->openResponseWindow($assignment, $assignable);

            $this->workflow->evaluate($assignable);

            return $assignment->fresh(['assignable', 'representative', 'requestPath']);
        });
    }

    public function reject(
        CourierAssignment $assignment,
        Representative $representative,
        ?int $rejectionReasonId = null,
        ?string $note = null
    ): CourierAssignment {
        $this->assertOwnedBy($assignment, $representative);
        $this->assertRespondable($assignment);

        if ($rejectionReasonId) {
            RejectionReason::query()->findOrFail($rejectionReasonId);
        }

        return \DB::transaction(function () use ($assignment, $rejectionReasonId, $note) {
            $assignment->update([
                'status' => CourierAssignmentStatus::REJECTED->value,
                'responded_at' => now(),
                'rejection_reason_id' => $rejectionReasonId,
                'rejection_note' => $note,
            ]);

            $this->runAutoActionIfNoActiveOffers($assignment);

            return $assignment->fresh();
        });
    }

    public function expire(CourierAssignment $assignment, string $reasonNote = 'auto'): CourierAssignment
    {
        return \DB::transaction(function () use ($assignment, $reasonNote) {
            $assignment->update([
                'status' => CourierAssignmentStatus::EXPIRED->value,
                'responded_at' => $assignment->responded_at ?? now(),
                'rejection_note' => $assignment->rejection_note ?? $reasonNote,
            ]);

            $this->runAutoActionIfNoActiveOffers($assignment);

            return $assignment->fresh();
        });
    }

    public function runAutoActionIfNoActiveOffers(CourierAssignment $assignment): void
    {
        $assignable = $assignment->assignable;

        $hasActiveOffers = $assignable->assignments()
            ->whereIn('status', [CourierAssignmentStatus::PENDING->value, CourierAssignmentStatus::ACCEPTED->value])
            ->exists();

        if ($hasActiveOffers) {
            return;
        }

        // Path-based flow: let the workflow fail the affected path and finalize the request.
        if ($assignable->requestPaths()->exists()) {
            $this->workflow->evaluate($assignable);

            return;
        }

        $this->executeAutoAction($assignment);
    }

    protected function assignableExecutionStarted($assignable): bool
    {
        if ($assignable instanceof \App\Models\OrderItem) {
            return ($assignable->status?->value ?? $assignable->status) === 'accepted';
        }

        if ($assignable instanceof \App\Models\ShipmentRequest) {
            return in_array($assignable->status?->value ?? $assignable->status, [
                \App\Enum\ShipmentRequestStatus::EXECUTING->value,
                \App\Enum\ShipmentRequestStatus::COMPLETED->value,
            ], true);
        }

        return false;
    }

    public function executeAutoAction(CourierAssignment $assignment): void
    {
        if ($assignment->auto_action_executed_at !== null) {
            return;
        }

        $assignable = $assignment->assignable;
        $action = $assignment->auto_action ?: $this->config->timeoutAutoAction();

        $assignable->assignments()
            ->where('id', '!=', $assignment->id)
            ->whereNull('auto_action_executed_at')
            ->update(['auto_action_executed_at' => now()]);

        $assignment->update(['auto_action_executed_at' => now()]);

        switch ($action) {
            case 'auto_assign_next':
                $result = $this->dispatchService->dispatch($assignable);

                if ($result->isEmpty()) {
                    $this->markNoMatch($assignable);
                }
                break;

            case 'auto_cancel':
                $this->cancelAssignable($assignable);
                break;

            case 'none':
            default:
                break;
        }
    }

    protected function openResponseWindow(CourierAssignment $accepted, $assignable): void
    {
        $closesAt = $this->workingHours->addWorkingHours(
            now(),
            $this->config->responseWindowWorkingHours(),
        );

        $assignable->assignments()
            ->where('id', '!=', $accepted->id)
            ->where('status', CourierAssignmentStatus::PENDING->value)
            ->update([
                'window_opens_at' => now(),
                'window_closes_at' => $closesAt,
                'response_window_working_hours' => $this->config->responseWindowWorkingHours(),
            ]);
    }

    protected function assertOwnedBy(CourierAssignment $assignment, Representative $representative): void
    {
        if ((int) $assignment->representative_id !== (int) $representative->id) {
            throw ValidationException::withMessages([
                'courier_assignment' => ['You are not the courier assigned to this offer.'],
            ]);
        }
    }

    protected function assertRespondable(CourierAssignment $assignment): void
    {
        if ($assignment->status !== CourierAssignmentStatus::PENDING) {
            throw ValidationException::withMessages([
                'courier_assignment' => ['This offer has already been responded to.'],
            ]);
        }

        if ($assignment->response_deadline_at !== null
            && $assignment->response_deadline_at->isPast()
        ) {
            throw ValidationException::withMessages([
                'courier_assignment' => ['The response deadline for this offer has passed.'],
            ]);
        }
    }

    protected function markNoMatch($assignable): void
    {
        $assignable->update([
            'dispatch_state' => DispatchState::NO_MATCH->value,
            'dispatch_state_at' => now(),
        ]);
    }

    protected function cancelAssignable($assignable): void
    {
        $assignable->update([
            'dispatch_state' => DispatchState::COMPLETED->value,
            'dispatch_state_at' => now(),
            'status' => 'cancelled',
        ]);
    }
}
