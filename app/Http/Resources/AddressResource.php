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
            'id'=>$this->id,
            'address'=>$this->address,
            'location'=>$this->location,
            'city'=>$this->city,
            'state'=>$this->state,
            'country'=>$this->country,
            'landmark'=>$this->landmark,
            'phone'=>$this->phone,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
        ];
    }
}
