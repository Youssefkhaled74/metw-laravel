<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EcommerceOrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $percentage = $this->product?->deposit_percentage ?? 0;
        $depositValue = ($this->unit_price * $this->quantity) * ($percentage / 100);

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->product_variant_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'returnable'=> $this->is_returnable,
            'shipment_price' => $this->shipment_price,
            // 'is_accepted_to_pay' => $this->is_shipment_accepted,

            // 'deposit_percentage' => $percentage,
            // 'deposit_value' => round($depositValue, 2),

            'product' => new ProductResource($this->whenLoaded('product')),
            'variant' => new ProductVariantResource($this->whenLoaded('variant')),
        ];
    }

}
