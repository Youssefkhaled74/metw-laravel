<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_number' => $this->item_number,
            'status' => $this->status,
            'est_price' => $this->est_price,
            'est_date' => $this->est_date,
            'requires_split' => $this->is_split,

            'shipment_company' => new ShipmentCompanyResource(
                $this->whenLoaded('shipmentCompany')
            ),
        ];
    }
}
