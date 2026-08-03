<?php

namespace App\Http\Resources;

use App\Models\RequestPath;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RequestPath */
class RequestPathResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $assignments = $this->relationLoaded('assignments')
            ? $this->assignments
            : $this->assignments()->get();

        return [
            'id' => $this->id,
            'type' => $this->type?->value ?? $this->type,
            'request_type' => $this->request_type?->value ?? $this->request_type,
            'status' => $this->status?->value ?? $this->status,
            'legs' => $this->legs,
            'total_cost' => $this->total_cost !== null ? (float) $this->total_cost : null,
            'currency' => $this->currency ?? 'EGP',
            'failure_reason' => $this->failure_reason,
            'courier_confirmed_at' => $this->courier_confirmed_at,
            'submitted_to_client_at' => $this->submitted_to_client_at,
            'client_selected_at' => $this->client_selected_at,
            'execution_started_at' => $this->execution_started_at,
            'executed_at' => $this->executed_at,
            'failed_at' => $this->failed_at,
            'assignments' => CourierAssignmentResource::collection($assignments),
        ];
    }
}
