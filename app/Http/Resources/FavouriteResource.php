<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ShipmentCompany;

class FavouriteResource extends JsonResource
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
            'user_id' => $this->user_id,
            // keep legacy field names in response
            'shipment_company_id' => $this->favouriteable_type === ShipmentCompany::class ? $this->favouriteable_id : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // keep legacy field name 'company'
            'company' => $this->when(
                $this->favouriteable_type === ShipmentCompany::class,
                new ShipmentCompanyResource($this->whenLoaded('favouriteable'))
            ),
        ];
    }
}
