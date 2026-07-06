<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'governorate_number' => $this->governorate_number,
            'name_ar' => $this->name_ar,
            'name' => $this->name,
            'capital_city_id' => $this->capital_city_id,
        ];
    }
}
