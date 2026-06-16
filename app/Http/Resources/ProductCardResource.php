<?php

namespace App\Http\Resources;

use App\Enum\ProductMediaType;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'price'        => $this->price,
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'stock'             => (float)$this->stock,
            'rating'            => (float) $this->rating,
            'reviews_count'     => $this->rating_count,
            'is_active'         => $this->is_active,
            'is_favourite'      => isset($this->is_favourite)
                ? (bool) $this->is_favourite
                : ($this->myFavourite !== null),
            'is_sale'           => $this->is_sale ?? false,

            // 🔹 Media
            'images' => ProductMediaResource::collection(
                $this->whenLoaded('media', function () {
                    return $this->media->where('type', \App\Enum\ProductMediaType::IMAGE);
                })
            ),

            'videos' => ProductMediaResource::collection(
                $this->whenLoaded('media', function () {
                    return $this->media->where('type', \App\Enum\ProductMediaType::VIDEO);
                })
            ),

            // 🔹 Variants
            'variants' => ProductVariantResource::collection(
                $this->whenLoaded('variants')
            ),

            // 🔹 Extract available colors from variants
            'available_colors' => $this->whenLoaded('variants', function () {
                $colors = $this->variants->loadMissing('color')->pluck('color')->filter();
                return $colors->unique('id')->values()->map(function ($color) {
                    return [
                        'id'   => $color->id,
                        'name' => $color->name,
                        'hex'  => $color->hex,
                    ];
                });
            }),

            // 🔹 Extract available sizes from variants
            'available_sizes' => $this->whenLoaded('variants', function () {
                $sizes = $this->variants->loadMissing('size')->pluck('size')->filter();
                return $sizes->unique('id')->values()->map(function ($size) {
                    return [
                        'id'    => $size->id,
                        'title' => $size->title,
                        'icon'  => $size->icon,
                    ];
                });
            }),

            // 🔹 Flags
            'has_color_options' => $this->whenLoaded(
                'variants',
                fn() =>
                $this->variants->contains(fn($v) => !is_null($v->color_id))
            ),

            'has_size_options' => $this->whenLoaded(
                'variants',
                fn() =>
                $this->variants->contains(fn($v) => !is_null($v->size_id))
            ),

            // 🔹 Relations
            'category' => $this->category ? [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,

            'vendor' => $this->vendor ? [
                'id'   => $this->vendor->id,
                'name' => $this->vendor->name,
                'logo' => $this->vendor->logo ? asset($this->vendor->logo) : null,
            ] : null,

            'reviews' => ProductReviewResource::collection(
                $this->whenLoaded('reviews')
            ),
        ];
    }
}
