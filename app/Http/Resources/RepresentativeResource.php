<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_type' => $this->account_type?->value ?? $this->account_type,
            'status' => $this->status?->value ?? $this->status,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->submitted_at,
            'reviewed_at' => $this->reviewed_at,
            'approved_at' => $this->approved_at,
            'suspended_at' => $this->suspended_at,
            'is_active' => $this->is_active,
            'metadata' => $this->metadata,
            'user' => new UserResource($this->whenLoaded('user')),
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                    'phone' => $this->warehouse->phone,
                    'full_address' => $this->warehouse->full_address,
                ];
            }),
            'work_types' => $this->whenLoaded('workTypes', function () {
                return $this->workTypes
                    ->map(fn ($workType) => $workType->work_type?->value ?? $workType->work_type)
                    ->values();
            }),
            'governorates' => $this->whenLoaded('governorates', function () {
                return $this->governorates->map(function ($governorate) {
                    return [
                        'id' => $governorate->id,
                        'governorate_number' => $governorate->governorate_number,
                        'name_ar' => $governorate->name_ar,
                        'name' => $governorate->name,
                        'capital_city_id' => $governorate->capital_city_id,
                    ];
                })->values();
            }),
            'cities' => $this->whenLoaded('cities', function () {
                return $this->cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name_ar' => $city->name_ar,
                        'name_en' => $city->name_en,
                        'name' => $city->name,
                        'governorate_id' => $city->governorate_id,
                        'is_capital' => $city->is_capital,
                    ];
                })->values();
            }),
            'vehicle' => $this->whenLoaded('vehicle', function () {
                if (! $this->vehicle) {
                    return null;
                }

                return [
                    'id' => $this->vehicle->id,
                    'transport_type_id' => $this->vehicle->transport_type_id,
                    'registration_number' => $this->vehicle->registration_number,
                    'license_number' => $this->vehicle->license_number,
                    'brand' => $this->vehicle->brand,
                    'model' => $this->vehicle->model,
                    'color' => $this->vehicle->color,
                    'manufacture_year' => $this->vehicle->manufacture_year,
                    'max_weight' => $this->vehicle->max_weight,
                    'max_volume' => $this->vehicle->max_volume,
                    'is_active' => $this->vehicle->is_active,
                    'notes' => $this->vehicle->notes,
                    'metadata' => $this->vehicle->metadata,
                    'transport_type' => $this->vehicle->relationLoaded('transportType') && $this->vehicle->transportType
                        ? new TransportTypeResource($this->vehicle->transportType)
                        : null,
                ];
            }),
            'documents' => RepresentativeMediaFileResource::collection($this->whenLoaded('mediaFiles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
