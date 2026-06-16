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
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'state_id' => $this->state_id,
            'city_id'=>$this->city_id,
            'zone_id' => $this->zone_id,
            'street_name'=>$this->street_name,
            'building'=>$this->building,
            'floor'=>$this->floor,
            'landmark'=>$this->landmark,
            'address_type'=>$this->address_type,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
            'state' => new StateResource($this->state),
            'city'=>new CityResource($this->city),
            'zone'=>new ZoneResource($this->zone),
            'is_default'=>$this->is_default
        ];
    }
}
