<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->translation(app()->getLocale());
        return [
            'id'    => $this->id,
            'name'=> $translation->name ?? $this->name,
            'slug'=> $translation->slug ?? $this->slug,
            'image' => $this->image ? asset($this->image) : null,
            'is_active' => $this->is_active,

            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
