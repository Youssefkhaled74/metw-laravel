<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
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
            'user_id' => $this->user_id,
            'generated_address_number' => $this->generated_address_number,
            'address_name' => $this->address_name,
            'governorate_id' => $this->governorate_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zone_id' => $this->zone_id,
            'district_or_village_name' => $this->district_or_village_name,
            'district_or_village_type' => $this->district_or_village_type,
            'street_name' => $this->street_name,
            'branch_from_street' => $this->branch_from_street,
            'building_number' => $this->building_number,
            'floor_number' => $this->floor_number,
            'building_name' => $this->building_name,
            'nearby_landmark' => $this->nearby_landmark,
            'address_description' => $this->address_description,
            'building' => $this->building,
            'floor' => $this->floor,
            'landmark' => $this->landmark,
            'address_type' => $this->address_type,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'state' => new StateResource($this->state),
            'city' => new CityResource($this->city),
            'zone' => new ZoneResource($this->zone),
            'full_address' => $this->full_address,
            'is_default' => $this->is_default,
        ];
    }
}
