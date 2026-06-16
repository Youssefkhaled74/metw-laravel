<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentCompanyResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'logo' => $this->logo ? asset($this->logo) : null,
            'facebook_url' => $this->facebook_url,
            'whatsapp_url' => $this->whatsapp_url,
            'is_active' => $this->is_active,
            'average_rating' => (float) ($this->reviews_avg_rate ?? 0),
            'count_reviews' => (int) ($this->reviews_count ?? 0),

            'is_favourite'  => $this->isFavourite(),

            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'coverage' => ShipmentLocationResource::collection(
                $this->whenLoaded('shipmentLocations')
            ),
            // 'coverages' => CoverageResource::collection($this->whenLoaded('coverages')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
