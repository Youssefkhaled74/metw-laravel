<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
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
            'city_id'=>$this->city_id,
        ];
    }
}
