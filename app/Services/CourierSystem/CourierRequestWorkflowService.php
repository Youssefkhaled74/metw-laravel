<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierAssignmentStatus;
use App\Enum\DispatchState;
use App\Enum\RequestPathStatus;
use App\Enum\ShipmentRequestStatus;
use App\Enum\SubmitterType;
use App\Models\CourierAssignment;
use App\Models\OrderItem;
use App\Models\RequestPath;
use App\Models\ShipmentRequest;
use Illuminate\Support\Collection;

/**
 * Drives the request / path state machine (Sections 3 & 4):
 *  - auto-approves the best courier per leg once every leg of a path is accepted,
 *  - fails paths with no acceptance,
 *  - presents successful paths to the submitter,
 *  - closes the request with the failure text when all paths fail,
 *  - handles path selection, advance payment and execution start
 *    (User needs admin payment confirmation, Seller is enough).
 */
class CourierRequestWorkflowService
{
    public function __construct(
        protected CourierSystemConfigService $config
    ) {}

    /**
     * Idempotent evaluation of the current state of a request.
     */
    public function evaluate(ShipmentRequest|OrderItem $assignable): void
    {
        $this->confirmReadyPaths($assignable);
        $this->failDeadPaths($assignable);

        if ($assignable instanceof ShipmentRequest) {
            $this->finalizePaths($assignable);
        } elseif ($assignable instanceof OrderItem) {
            $this->finalizeOrderItem($assignable);
        }
    }

    /**
     * Market-app order items have no submitter to pick a path: as soon as a path
     * is fully confirmed we start executing it with the confirmed couriers.
     */
    public function finalizeOrderItem(OrderItem $orderItem): void
    {
        if ($orderItem->representative_id !== null) {
            return;
        }

        $path = $orderItem->requestPaths()
            ->where('status', RequestPathStatus::COURIER_CONFIRMED->value)
            ->first();

        if (! $path) {
            return;
        }

        $firstConfirmed = $path->assignments()
            ->where('status', CourierAssignmentStatus::CONFIRMED->value)
            ->orderBy('leg_type')
            ->first();

        $path->update([
            'status' => RequestPathStatus::EXECUTING->value,
            'execution_started_at' => now(),
        ]);

        $orderItem->update([
            'status' => 'accepted',
            'representative_id' => $firstConfirmed?->representative_id,
            'accepted_at' => now(),
            'accepted_fee' => $this->firstConfirmedFee($path),
            'dispatch_state' => DispatchState::COMPLETED->value,
            'dispatch_state_at' => now(),
        ]);
    }

    /**
     * Auto-approve one courier per leg for every path whose legs are all accepted.
     */
    public function confirmReadyPaths(ShipmentRequest|OrderItem $assignable): void
    {
        $paths = $assignable->requestPaths()
            ->where('status', RequestPathStatus::MATCHING->value)
            ->get();

        foreach ($paths as $path) {
            if (! $path->hasConfirmedCourierForEveryLeg()) {
                continue;
            }

            $this->confirmPath($path);
        }
    }

    public function confirmPath(RequestPath $path): void
    {
        $legs = $path->legs ?? [];
        $totalCost = 0;

        foreach ($legs as $leg) {
            $best = $this->bestAcceptedAssignment($path, $leg);

            if (! $best) {
                return;
            }

            $fee = $this->assignmentFee($best) ?? 0;
            $totalCost += $fee;

            $best->update(['status' => CourierAssignmentStatus::CONFIRMED->value]);

            // Cancel the other acceptors on the same leg of this path.
            $path->assignments()
                ->where('leg_type', $leg)
                ->where('id', '!=', $best->id)
                ->where('status', CourierAssignmentStatus::ACCEPTED->value)
                ->update(['status' => CourierAssignmentStatus::CANCELLED->value]);
        }

        $path->update([
            'status' => RequestPathStatus::COURIER_CONFIRMED->value,
            'total_cost' => $totalCost,
            'courier_confirmed_at' => now(),
            'metadata' => array_merge((array) $path->metadata, [
                'confirmed_couriers' => $path->assignments()
                    ->where('status', CourierAssignmentStatus::CONFIRMED->value)
                    ->get()
                    ->map(fn (CourierAssignment $assignment) => [
                        'leg_type' => $assignment->leg_type?->value ?? $assignment->leg_type,
                        'representative_id' => $assignment->representative_id,
                        'fee' => $this->assignmentFee($assignment),
                    ])
                    ->values()
                    ->all(),
            ]),
        ]);
    }

    /**
     * Mark as failed any matching path that no longer has pending or accepted offers.
     */
    public function failDeadPaths(ShipmentRequest|OrderItem $assignable): void
    {
        $paths = $assignable->requestPaths()
            ->where('status', RequestPathStatus::MATCHING->value)
            ->get();

        foreach ($paths as $path) {
            $hasActive = $path->assignments()
                ->whereIn('status', [
                    CourierAssignmentStatus::PENDING->value,
                    CourierAssignmentStatus::ACCEPTED->value,
                    CourierAssignmentStatus::CONFIRMED->value,
                ])
                ->exists();

            if (! $hasActive) {
                $path->update([
                    'status' => RequestPathStatus::FAILED->value,
                    'failure_reason' => 'no_courier_acceptance',
                    'failed_at' => now(),
                ]);
            }
        }
    }

    /**
     * After all paths settled, present the successful ones or close the request.
     */
    public function finalizePaths(ShipmentRequest $request): void
    {
        if ($request->selected_request_path_id !== null) {
            return;
        }

        if (! in_array($request->status?->value ?? $request->status, [
            ShipmentRequestStatus::SUBMITTED->value,
            ShipmentRequestStatus::MATCHING->value,
            ShipmentRequestStatus::AWAITING_PATH_SELECTION->value,
        ], true)) {
            return;
        }

        $successful = $request->requestPaths()
            ->whereIn('status', [
                RequestPathStatus::COURIER_CONFIRMED->value,
                RequestPathStatus::SUBMITTED_TO_CLIENT->value,
            ])
            ->get();

        if ($successful->isEmpty()) {
            $this->closeUnavailable($request);

            return;
        }

        $request->requestPaths()
            ->where('status', RequestPathStatus::COURIER_CONFIRMED->value)
            ->update([
                'status' => RequestPathStatus::SUBMITTED_TO_CLIENT->value,
                'submitted_to_client_at' => now(),
            ]);

        $request->update([
            'status' => ShipmentRequestStatus::AWAITING_PATH_SELECTION->value,
            'paths_evaluated_at' => now(),
            'dispatch_state' => DispatchState::DISPATCHED->value,
        ]);
    }

    public function closeUnavailable(ShipmentRequest $request): void
    {
        $message = $request->isFastDelivery()
            ? $this->config->failureDeliveryMessage()
            : $this->config->failureShippingMessage();

        $request->update([
            'status' => ShipmentRequestStatus::FAILED_UNAVAILABLE->value,
            'failure_text' => $message,
            'closed_at' => now(),
            'paths_evaluated_at' => $request->paths_evaluated_at ?? now(),
            'dispatch_state' => DispatchState::NO_MATCH->value,
        ]);
    }

    /**
     * Submitter chooses one of the presented paths.
     */
    public function selectPath(ShipmentRequest $request, RequestPath $path): ShipmentRequest
    {
        if ((int) $path->pathable_id !== (int) $request->id || $path->pathable_type !== ShipmentRequest::class) {
            throw new \InvalidArgumentException('The selected path does not belong to this request.');
        }

        if (($request->status?->value ?? $request->status) !== ShipmentRequestStatus::AWAITING_PATH_SELECTION->value) {
            throw new \InvalidArgumentException('This request is not awaiting a path selection.');
        }

        if (! in_array($path->status?->value ?? $path->status, [
            RequestPathStatus::SUBMITTED_TO_CLIENT->value,
            RequestPathStatus::COURIER_CONFIRMED->value,
        ], true)) {
            throw new \InvalidArgumentException('This path is not available for selection.');
        }

        $path->update([
            'status' => RequestPathStatus::CLIENT_SELECTED->value,
            'client_selected_at' => now(),
        ]);

        // Cancel the other paths.
        $request->requestPaths()
            ->where('id', '!=', $path->id)
            ->whereNotIn('status', [RequestPathStatus::FAILED->value, RequestPathStatus::CANCELLED->value])
            ->update(['status' => RequestPathStatus::CANCELLED->value]);

        $request->assignments()
            ->where('status', CourierAssignmentStatus::PENDING->value)
            ->update(['status' => CourierAssignmentStatus::CANCELLED->value]);

        $request->update([
            'selected_request_path_id' => $path->id,
        ]);

        // Seller approval is enough to start execution; a User must pay first.
        if ($this->isSellerRequest($request)) {
            $this->startExecution($request);
        } else {
            $request->update(['status' => ShipmentRequestStatus::AWAITING_ADVANCE->value]);
        }

        return $request->fresh(['requestPaths', 'selectedPath', 'submitter']);
    }

    public function startExecution(ShipmentRequest $request): ShipmentRequest
    {
        $path = $request->selectedPath;

        if (! $path) {
            throw new \InvalidArgumentException('No path selected for this request.');
        }

        $path->update([
            'status' => RequestPathStatus::EXECUTING->value,
            'execution_started_at' => now(),
        ]);

        $request->update([
            'status' => ShipmentRequestStatus::EXECUTING->value,
            'execution_started_at' => now(),
            'dispatch_state' => DispatchState::COMPLETED->value,
            'dispatch_state_at' => now(),
        ]);

        // Set the executing representative from the first confirmed leg courier.
        $firstConfirmed = $path->assignments()
            ->where('status', CourierAssignmentStatus::CONFIRMED->value)
            ->orderBy('leg_type')
            ->first();

        if ($firstConfirmed) {
            $request->update([
                'representative_id' => $firstConfirmed->representative_id,
                'accepted_at' => now(),
            ]);
        }

        return $request->fresh(['requestPaths', 'selectedPath', 'representative']);
    }

    public function isSellerRequest(ShipmentRequest $request): bool
    {
        return ($request->submitter_type?->value ?? $request->submitter_type) === SubmitterType::SELLER->value;
    }

    public function isUserRequest(ShipmentRequest $request): bool
    {
        return ($request->submitter_type?->value ?? $request->submitter_type) === SubmitterType::USER->value;
    }

    protected function bestAcceptedAssignment(RequestPath $path, string $leg): ?CourierAssignment
    {
        return $path->assignments()
            ->where('leg_type', $leg)
            ->where('status', CourierAssignmentStatus::ACCEPTED->value)
            ->get()
            ->sortBy(fn (CourierAssignment $assignment) => [
                $this->assignmentFee($assignment) ?? PHP_FLOAT_MAX,
                $assignment->responded_at ?? PHP_INT_MAX,
            ])
            ->first();
    }

    protected function assignmentFee(CourierAssignment $assignment): ?float
    {
        $metadata = (array) $assignment->metadata;
        $fee = $metadata['accepted_fee'] ?? null;

        return $fee !== null ? (float) $fee : null;
    }

    protected function firstConfirmedFee(RequestPath $path): ?float
    {
        $metadata = (array) $path->metadata;
        $couriers = $metadata['confirmed_couriers'] ?? [];

        return isset($couriers[0]['fee']) ? (float) $couriers[0]['fee'] : null;
    }
}
