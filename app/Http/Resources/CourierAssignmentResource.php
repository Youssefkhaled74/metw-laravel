<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourierAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $representative = $this->whenLoaded('representative');

        return [
            'id' => $this->id,
            'request_path_id' => $this->request_path_id,
            'leg_type' => $this->leg_type?->value ?? $this->leg_type,
            'representative_id' => $this->representative_id,
            'representative_name' => $representative
                ? trim(implode(' ', array_filter([
                    $representative->first_name,
                    $representative->father_name,
                    $representative->last_name,
                ])))
                : null,
            'status' => $this->status?->value ?? $this->status,
            'accepted_fee' => ($this->metadata['accepted_fee'] ?? null) !== null
                ? (float) $this->metadata['accepted_fee']
                : null,
            'offered_at' => $this->offered_at,
            'responded_at' => $this->responded_at,
            'response_deadline_at' => $this->response_deadline_at,
            'window_opens_at' => $this->window_opens_at,
            'window_closes_at' => $this->window_closes_at,
            'rejection_note' => $this->rejection_note,
        ];
    }
}
