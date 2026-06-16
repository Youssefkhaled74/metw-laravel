<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'name'=>$this->name,
            'email'=>$this->email,
            'country_code' => $this->country_code,
            'phone'=>$this->phone,
            'address'=>$this->address,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
            'logo'=> $this->logo ? asset($this->logo) : null,
            'email_verified'=>$this->email_verified,
            'phone_verified'=>$this->phone_verified,
            'is_active'=>$this->is_active,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
