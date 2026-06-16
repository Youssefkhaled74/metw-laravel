<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price ?? $this->product->price,
            'stock' => $this->stock,
            'color' => $this->when($this->color, function () {
                return [
                    'id' => $this->color->id,
                    'name' => $this->color->name,
                    'hex' => $this->color->hex,
                ];
            }),
            'color_images' => ProductMediaResource::collection(
                $this->whenLoaded('media', fn() => $this->media->where('type', \App\Enum\ProductMediaType::COLOR_IMAGE))
            ),
            'size' => $this->when($this->size, function () {
                return [
                    'id' => $this->size->id,
                    'title' => $this->size->title,
                    'icon' => $this->size->icon,
                ];
            }),
        ];
    }
}

