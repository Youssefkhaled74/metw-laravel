<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $address = null;

        if ($this->relationLoaded('primaryAddress') && $this->primaryAddress) {
            $address = new ShipmentContactAddressResource($this->primaryAddress);
        } elseif ($this->relationLoaded('foundationAddresses') && $this->foundationAddresses->isNotEmpty()) {
            $address = new ShipmentContactAddressResource(
                $this->foundationAddresses->firstWhere('is_primary', true) ?? $this->foundationAddresses->first()
            );
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type?->value ?? $this->type,
            'contact_number' => $this->contact_number,
            'full_name' => $this->full_name,
            'primary_mobile' => $this->primary_mobile,
            'secondary_mobile' => $this->secondary_mobile,
            'address' => $address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
