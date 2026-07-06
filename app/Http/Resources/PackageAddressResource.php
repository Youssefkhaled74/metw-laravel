<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'generated_address_number' => $this->generated_address_number,
            'address_name' => $this->address_name,
            'address' => $this->address,
            'location' => $this->location,
            'phone' => $this->phone,
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
            'city_id' => $this->city_id,
            'state_id' => $this->state_id,
            'governorate_id' => $this->governorate_id,
            'country_id' => $this->country_id,
            'zone_id' => $this->zone_id,
            'full_address' => $this->full_address ?? null,
        ];
    }
}
