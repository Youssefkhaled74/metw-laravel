<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'user_id' => $this->user_id,
            'status' => $this->status?->value ?? $this->status,
            'notes' => $this->notes,
            'submitted_at' => $this->submitted_at,
            'metadata' => $this->metadata,
            'sender_contact' => $this->whenLoaded('senderContact', fn () => new ShipmentContactResource($this->senderContact)),
            'receiver_contact' => $this->whenLoaded('receiverContact', fn () => new ShipmentContactResource($this->receiverContact)),
            'packages_count' => $this->whenCounted('packages'),
            'packages' => ShipmentRequestPackageResource::collection($this->whenLoaded('packages')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
