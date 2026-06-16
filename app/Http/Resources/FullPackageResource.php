<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // ================= Basic package info =================
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

            // ================= Addresses =================
            'pickup_address' => new PackageAddressResource(
                $this->whenLoaded('pickupAddress')
            ),
            'dropoff_address' => new PackageAddressResource(
                $this->whenLoaded('dropoffAddress')
            ),

            // ================= Details =================
            'details' => new PackageDetailsResource(
                $this->whenLoaded('packageDetails')
            ),

            // ================= Shipment company =================
            'shipment_company' => new ShipmentCompanyResource(
                $this->whenLoaded('shipmentCompany')
            ),

            // ================= Images =================
            'images' => PackageImageResource::collection(
                $this->whenLoaded('images')
            ),

            // ================= Tracking =================
            'trackings' => PackageTrackingResource::collection(
                $this->whenLoaded('trackings')
            ),

            // ================= Meta =================
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
