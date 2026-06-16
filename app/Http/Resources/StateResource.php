<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'         => $this->id,
            'name'       => $locale === 'ar' ? $this->name_ar : $this->name_en,
            'country_id' => $this->country_id,
            // 'cities'=> CityResource::collection($this->whenLoaded('cities'))
        ];
    }
}
