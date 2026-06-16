<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\PaymentStatus;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstItem = $this->orderItems->first();
        $package   = optional($firstItem)->package;

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'is_paid' => in_array(
                $this->payment_status,
                [PaymentStatus::PAID->value, PaymentStatus::SUCCESS->value]
            ),
            'total_price' => $this->total_price,
            'paid_amount' => $this->paid_amount,
            'remaining_amount' => $this->remaining_amount,
            'discount_price' => $this->discount_price,
            'final_price' => $this->final_price,
            'created_at' => $this->created_at,

            // ✅ shipment company once
            'shipment_company' => new ShipmentCompanyResource(
                $this->whenLoaded('shipmentCompany')
            ),

            // ✅ addresses once (same as cart)
            'pickup_address' => new AddressResource(
                optional($package)->pickupAddress
            ),

            'dropoff_address' => new AddressResource(
                optional($package)->dropoffAddress
            ),

            // ✅ package details once
            'details' => new PackageDetailsResource(
                optional($package)->packageDetails
            ),

            // ✅ items (light, no duplication)
            'items' => OrderItemResource::collection(
                $this->whenLoaded('orderItems')
            ),
        ];
    }
}

