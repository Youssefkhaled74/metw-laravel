<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'order_id'=>$this->order_id,
            'shipment_company_id'=>$this->shipment_company_id,
            'user_id'=>$this->user_id,
            'rate'=>$this->rate,
            'comment'=>$this->comment,
            'user'=>new UserResource($this->whenLoaded('user')),
            // 'order'=>new OrderResource($this->order),
            'shipmentCompany'=>new ShipmentCompanyResource($this->whenLoaded('shipmentCompany')),
        ];
    }
}
