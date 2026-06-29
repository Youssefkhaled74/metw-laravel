<?php

namespace App\Http\Resources;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translation(app()->getLocale());
        $categoryTranslation = $this->category?->translation(app()->getLocale());
        $finalPrice = $this->price;

        if ($this->discount_percentage > 0) {
            $startDate = $this->discount_start;
            $endDate = $this->discount_end;
            $now = now();

            if ($startDate && $endDate && $startDate <= $now && $now <= $endDate) {
                $finalPrice = $this->price - ($this->price * $this->discount_percentage / 100);
            }
        }

        return [
            'id' => $this->id,
            'name' => $translation?->name ?? $this->name,
            'slug' => $translation?->slug ?? $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'final_price' => $finalPrice,
            'short_description' => $translation?->short_description ?? $this->short_description,
            'description' => $translation?->description ?? $this->description,
            'stock' => (float) $this->stock,
            'rating' => (float) $this->rating,
            'reviews_count' => $this->rating_count,
            'is_active' => $this->is_active,
            'is_favourite' => isset($this->is_favourite)
                ? (bool) $this->is_favourite
                : ($this->myFavourite !== null),
            'is_sale' => $this->is_sale ?? false,
            'has_variants' => $this->has_variants,
            'has_deposit' => $this->has_deposit,
            'deposit_percentage' => $this->deposit_percentage,

            'images' => ProductMediaResource::collection(
                $this->whenLoaded('media', fn () => $this->media->where('type', \App\Enum\ProductMediaType::IMAGE))
            ),
            'extra_images' => ProductMediaResource::collection(
                $this->whenLoaded('media', fn () => $this->media->where('type', \App\Enum\ProductMediaType::EXTRA_IMAGE))
            ),
            'videos' => ProductMediaResource::collection(
                $this->whenLoaded('media', fn () => $this->media->where('type', \App\Enum\ProductMediaType::VIDEO))
            ),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),

            'available_colors' => $this->whenLoaded('variants', function () {
                $colors = $this->variants->loadMissing('color')->pluck('color')->filter();

                return $colors->unique('id')->values()->map(function ($color) {
                    return [
                        'id' => $color->id,
                        'name' => $color->name,
                        'hex' => $color->hex,
                    ];
                });
            }),

            'available_sizes' => $this->whenLoaded('variants', function () {
                $sizes = $this->variants->loadMissing('size')->pluck('size')->filter();

                return $sizes->unique('id')->values()->map(function ($size) {
                    return [
                        'id' => $size->id,
                        'title' => $size->title,
                        'icon' => $size->icon,
                    ];
                });
            }),

            'has_color_options' => $this->whenLoaded(
                'variants',
                fn () => $this->variants->contains(fn ($variant) => ! is_null($variant->color_id))
            ),
            'has_size_options' => $this->whenLoaded(
                'variants',
                fn () => $this->variants->contains(fn ($variant) => ! is_null($variant->size_id))
            ),

            'main_category' => $this->whenLoaded('maincategory', function () {
                $mainCategoryTranslation = $this->maincategory?->translation(app()->getLocale());

                return [
                    'id' => $this->maincategory->id,
                    'name' => $mainCategoryTranslation?->name ?? $this->maincategory->name,
                    'slug' => $mainCategoryTranslation?->slug ?? $this->maincategory->slug,
                ];
            }),

            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $categoryTranslation?->name ?? $this->category->name,
                'slug' => $categoryTranslation?->slug ?? $this->category->slug,
            ] : null,

            'vendor' => $this->vendor ? [
                'id' => $this->vendor->id,
                'name' => $this->vendor->name,
                'logo' => $this->vendor->logo ? asset($this->vendor->logo) : null,
            ] : null,

            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => app()->getLocale() === 'ar' ? $this->brand->name_ar : $this->brand->name_en,
                'image' => $this->brand->image ? asset($this->brand->image) : null,
            ] : null,

            'related' => ProductCardResource::collection($this->whenLoaded('relatedProducts')),
            'reviews' => ProductReviewResource::collection($this->whenLoaded('reviews')),

            'returnable' => $this->is_returnable,
            'return_fee' => (float) $this->return_fee,
            'return_validity' => (float) $this->return_validity,
            'features' => $this->features,
            'product_info' => $this->product_info,
            'usage_description' => $this->usage_description,
            'parts_description' => $this->parts_description,
            'material_description' => $this->material_description,
            'dimensions' => $this->dimensions,
            'weight' => $this->weight,
            'volume' => $this->volume,
            'origin_country' => $this->origin_country,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'expiry_period' => $this->expiry_period,
            'discount_percentage' => $this->discount_percentage,
            'discount_start' => $this->discount_start,
            'discount_end' => $this->discount_end,
            'free_shipping' => $this->free_shipping,
            'free_shipping_min_order' => $this->free_shipping_min_order,
            'shipment_type' => $this->shipment_type,
            'shipment_description' => $this->shipment_description,
            'shipment_dimensions' => $this->shipment_dimensions,
            'shipment_weight' => $this->shipment_weight,
            'storage_conditions' => $this->storage_conditions ?? [],
            'delivery_zones' => $this->mapDeliveryZones(),
            'delivery_options' => $this->delivery_options ?? [],
            'subcategories_level1' => $this->subcategories_level1,
            'subcategory_level2' => $this->subcategory_level2,
            'auto_discount_end_date' => $this->auto_discount_end_date,
            'free_shipping_price' => $this->free_shipping_price,
            'package_length' => $this->package_length,
            'package_width' => $this->package_width,
            'package_height' => $this->package_height,
            'package_weight' => $this->package_weight,

            'shipping_profile' => $this->whenLoaded(
                'shippingProfile',
                fn () => new ProductShippingProfileResource($this->shippingProfile)
            ),
            'return_policy' => $this->whenLoaded(
                'returnPolicy',
                fn () => new ProductReturnPolicyResource($this->returnPolicy)
            ),
            'shipping_fee_policy' => $this->whenLoaded(
                'shippingFeePolicy',
                fn () => new ProductShippingFeeResource($this->shippingFeePolicy)
            ),
        ];
    }

    protected function mapDeliveryZones(): array
    {
        if (! $this->delivery_zones || ! is_array($this->delivery_zones)) {
            return [];
        }

        $zones = Zone::whereIn('id', $this->delivery_zones)->get();
        $locale = app()->getLocale();

        return $zones->map(function ($zone) use ($locale) {
            return [
                'id' => $zone->id,
                'name' => $locale === 'ar' ? $zone->name_ar : $zone->name_en,
            ];
        })->values()->all();
    }
}
