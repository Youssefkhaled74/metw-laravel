<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreviewFullPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package_number' => $this->package_number,
            'weight' => $this->weight,

            // ================= IDs =================
            'type_id' => $this->type_id,
            'size_id' => $this->size_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'delivery_type_id' => $this->delivery_type_id,
            'consignment_type_id' => $this->consignment_type_id,

            // ================= Relations =================
            'type' => new PackageTypeResource($this->whenLoaded('type')),
            'size' => new SizeResource($this->whenLoaded('size')),
            'delivery_type' => new DeliveryTypeResource($this->whenLoaded('deliveryType')),
            'consignment_type' => new ConsignmentTypeResource($this->whenLoaded('consignmentType')),

            'category' => new MainCategoryResource($this->whenLoaded('category')),
            'sub_category' => new CategoryResource($this->whenLoaded('subCategory')),

            // 'pickup_address' => $this->whenLoaded('pickupAddress'),
            // 'dropoff_address' => $this->whenLoaded('dropoffAddress'),
            // 'package_details' => $this->whenLoaded('packageDetails'),

            // ================= Images =================
            'images' => $this->whenLoaded('images')->map(function($image) {
                return [
                    'id' => $image->id,
                    'image' => asset($image->image), // full URL
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
