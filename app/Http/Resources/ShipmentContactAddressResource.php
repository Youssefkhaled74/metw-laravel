<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentContactAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'governorate_id' => $this->governorate_id,
            'city_id' => $this->city_id,
            'zone_id' => $this->zone_id,
            'postal_code' => $this->postal_code,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'street_name' => $this->street_name,
            'building' => $this->building,
            'floor' => $this->floor,
            'landmark' => $this->landmark,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'country' => $this->whenLoaded('country', fn () => [
                'id' => $this->country?->id,
                'name_ar' => $this->country?->name_ar,
                'name_en' => $this->country?->name_en,
            ]),
            'state' => $this->whenLoaded('state', fn () => [
                'id' => $this->state?->id,
                'name_ar' => $this->state?->name_ar,
                'name_en' => $this->state?->name_en,
            ]),
            'governorate' => $this->whenLoaded('governorate', fn () => [
                'id' => $this->governorate?->id,
                'name_ar' => $this->governorate?->name_ar,
                'name' => $this->governorate?->name,
            ]),
            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city?->id,
                'name_ar' => $this->city?->name_ar,
                'name_en' => $this->city?->name_en,
                'name' => $this->city?->name,
                'is_capital' => $this->city?->is_capital,
            ]),
            'zone' => $this->whenLoaded('zone', fn () => [
                'id' => $this->zone?->id,
                'name_ar' => $this->zone?->name_ar,
                'name_en' => $this->zone?->name_en,
            ]),
        ];
    }
}
