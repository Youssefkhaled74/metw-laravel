<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $package = $this->package;

        return [
            'id' => $this->id,

            'package_number' => $package->package_number,
            'weight' => $package->weight,

            // ✅ CORRECT
            'type' => $package->relationLoaded('type')
                ? new PackageTypeResource($package->type)
                : null,

            'size' => $package->relationLoaded('size')
                ? new SizeResource($package->size)
                : null,

            'delivery_type' => $package->relationLoaded('deliveryType')
                ? new DeliveryTypeResource($package->deliveryType)
                : null,

            'consignment_type' => $package->relationLoaded('consignmentType')
                ? new ConsignmentTypeResource($package->consignmentType)
                : null,

            'category' => $package->relationLoaded('category')
                ? new MainCategoryResource($package->category)
                : null,

            'sub_category' => $package->relationLoaded('subCategory')
                ? new CategoryResource($package->subCategory)
                : null,

            'images' => $package->relationLoaded('images')
                ? PackageImageResource::collection($package->images)
                : [],
        ];
    }
}
