<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EcommerceCartItemResource;

class EcommerceCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items_count' => $this->items_count,
            'total_price' => $this->total_price,
            'items' => EcommerceCartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
