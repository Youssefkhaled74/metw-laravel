<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description' => $this->description,
            'max_weight' => $this->max_weight,
            'max_volume' => $this->max_volume,
            'category_1_available' => $this->category_1_available,
            'category_2_available' => $this->category_2_available,
            'category_3_available' => $this->category_3_available,
            'requires_driving_license' => $this->requires_driving_license,
            'requires_vehicle_license' => $this->requires_vehicle_license,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
