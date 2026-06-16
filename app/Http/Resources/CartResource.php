<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ShipmentCompanyResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstItem = $this->items->first();
        $package   = optional($firstItem)->package;

        // Safely get shipment company:
        // 1. Use cart's shipment company if exists
        // 2. Otherwise, use pickup company from the first item's route if exists
        $shipmentCompany = $this->shipmentCompany
            ?? optional(optional($firstItem)->route)->pickupCompany;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items_count' => $this->items_count,
            'item_total_price' => $this->item_total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'shipment_company_id' => $shipmentCompany->id ?? null,
            'est_date' => optional($firstItem)->est_date,
            'est_price' => optional($firstItem)->est_price,
            'requires_split' => optional($firstItem)->requires_split,

            'pickup_address' => new AddressResource(optional($package)->pickupAddress),
            'dropoff_address' => new AddressResource(optional($package)->dropoffAddress),

            'shipment_company' => new ShipmentCompanyResource($shipmentCompany),

            'details' => new PackageDetailsResource(optional($package)->packageDetails),

            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}

