<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnRequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // // لو الـ resource متحمل من ReturnRequest (للـ orders?status=returned)
        // // اعرضه بنفس شكل EcommerceOrderItemResource
        // if ($this->resource instanceof \App\Models\ReturnRequestItem) {
        //     $unitPrice = $this->return_quantity > 0
        //         ? $this->return_price / $this->return_quantity
        //         : 0;

        //     return [
        //         'id' => $this->id,
        //         'product_id' => $this->orderItem->product_id ?? null,
        //         'variant_id' => $this->orderItem->product_variant_id ?? null,
        //         'quantity' => $this->return_quantity,
        //         'unit_price' => $unitPrice,
        //         'total_price' => $this->return_price,
        //         'product' => new ProductResource($this->whenLoaded('orderItem.product')),
        //         'variant' => new ProductVariantResource($this->whenLoaded('orderItem.variant')),
        //     ];
        // }

        // الـ default response (للـ return requests endpoints)
        return [
            'id' => $this->id,
            'quantity' => $this->return_quantity,
            'total_price' => $this->return_price,
            'return_reason' => $this->return_reason,
            // 'order_item' => new EcommerceOrderItemResource($this->whenLoaded('orderItem')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
