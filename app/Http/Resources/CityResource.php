<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$locale === 'ar' ? $this->name_ar : $this->name_en,
            'state_id'=>$this->state_id,
            // 'zones'=>ZoneResource::collection($this->whenLoaded('zones'))
        ];
    }
}
