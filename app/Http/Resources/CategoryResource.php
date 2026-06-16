<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->translation(app()->getLocale());
        return [
            'id'=>$this->id,
            'name'=>$translation?->name ?? $this->name,
            'slug'=>$translation?->slug ?? $this->slug,
            'image'=>asset($this->image),
            'type'=>$this->type,
            'is_active'=>$this->is_active,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
