<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShippingFeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'free_shipping' => $this->free_shipping,
            'free_shipping_min_order' => $this->free_shipping_min_order,
            'free_shipping_price' => $this->free_shipping_price,
        ];
    }
}
