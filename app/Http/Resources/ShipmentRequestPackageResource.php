<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentRequestPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_request_id' => $this->shipment_request_id,
            'package_name' => $this->package_name,
            'package_type' => $this->package_type,
            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'declared_value' => $this->declared_value,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'images' => RepresentativeMediaFileResource::collection($this->whenLoaded('mediaFiles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
