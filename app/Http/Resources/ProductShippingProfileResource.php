<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShippingProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'shipment_type' => $this->shipment_type,
            'shipment_description' => $this->shipment_description,
            'shipment_dimensions' => $this->shipment_dimensions,
            'shipment_weight' => $this->shipment_weight,
            'package_length' => $this->package_length,
            'package_width' => $this->package_width,
            'package_height' => $this->package_height,
            'package_weight' => $this->package_weight,
            'storage_conditions' => $this->storage_conditions ?? [],
            'delivery_zones' => $this->delivery_zones ?? [],
            'delivery_options' => $this->delivery_options ?? [],
        ];
    }
}
