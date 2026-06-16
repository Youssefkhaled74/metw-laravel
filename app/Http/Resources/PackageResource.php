<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package_number' => $this->package_number,
            'type_id' => $this->type_id,
            'size_id' => $this->size_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'weight' => $this->weight,
            'shipment_company_id' => $this->shipment_company_id,
            'delivery_type_id' => $this->delivery_type_id,
            'consignment_type_id' => $this->consignment_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
