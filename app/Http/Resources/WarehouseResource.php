<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'generated_address_number' => $this->generated_address_number,
            'address_name' => $this->address_name,
            'country_id' => $this->country_id,
            'governorate_id' => $this->governorate_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'district_or_village_name' => $this->district_or_village_name,
            'district_or_village_type' => $this->district_or_village_type,
            'street_name' => $this->street_name,
            'branch_from_street' => $this->branch_from_street,
            'building_number' => $this->building_number,
            'floor_number' => $this->floor_number,
            'building_name' => $this->building_name,
            'nearby_landmark' => $this->nearby_landmark,
            'address_description' => $this->address_description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_main' => (bool) ($this->is_main ?? false),
            'full_address' => $this->full_address ?? null,
        ];
    }
}
