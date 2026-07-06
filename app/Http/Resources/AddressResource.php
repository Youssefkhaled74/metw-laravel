<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'generated_address_number' => $this->generated_address_number,
            'address_name' => $this->address_name,
            'district_or_village_name' => $this->district_or_village_name,
            'district_or_village_type' => $this->district_or_village_type,
            'street_name' => $this->street_name,
            'branch_from_street' => $this->branch_from_street,
            'building_number' => $this->building_number,
            'floor_number' => $this->floor_number,
            'building_name' => $this->building_name,
            'nearby_landmark' => $this->nearby_landmark,
            'address_description' => $this->address_description,
            'address' => $this->full_address,
            'city' => $this->city,
            'state' => $this->state,
            'governorate' => $this->governorate,
            'country' => $this->country,
            'phone' => $this->contact_phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
