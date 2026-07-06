<?php

namespace App\Http\Resources;

use App\Enum\RepresentativeDocumentType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_number' => $this->account_number,
            'account_opened_at' => $this->account_opened_at,
            'account_type' => $this->account_type?->value ?? $this->account_type,
            'status' => $this->status?->value ?? $this->status,
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'last_name' => $this->last_name,
            'full_name' => trim(collect([$this->first_name, $this->father_name, $this->last_name])->filter()->implode(' ')),
            'main_mobile' => $this->phone,
            'second_mobile' => $this->second_phone,
            'email' => $this->user?->email,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'address' => $this->address,
            'village_service' => $this->village_service,
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
                    ->map(fn ($workType) => [
                        'id' => $workType->id,
                        'code' => $workType->work_type,
                        'name_en' => $workType->relationLoaded('option') && $workType->option ? $workType->option->name_en : null,
                        'name_ar' => $workType->relationLoaded('option') && $workType->option ? $workType->option->name_ar : null,
                        'name' => $workType->relationLoaded('option') && $workType->option ? $workType->option->name : $workType->work_type,
                    ])
                    ->values();
            }),
            'governorates' => $this->whenLoaded('governorates', function () {
                return $this->governorates->map(fn ($governorate) => [
                    'id' => $governorate->id,
                    'governorate_number' => $governorate->governorate_number,
                    'name_ar' => $governorate->name_ar,
                    'name' => $governorate->name,
                    'capital_city_id' => $governorate->capital_city_id,
                ])->values();
            }),
            'cities' => $this->whenLoaded('cities', function () {
                return $this->cities->map(fn ($city) => [
                    'id' => $city->id,
                    'name_ar' => $city->name_ar,
                    'name_en' => $city->name_en,
                    'name' => $city->name,
                    'governorate_id' => $city->governorate_id,
                    'is_capital' => $city->is_capital,
                ])->values();
            }),
            'vehicle' => $this->whenLoaded('vehicle', function () {
                if (! $this->vehicle) {
                    return null;
                }

                return [
                    'id' => $this->vehicle->id,
                    'transport_type_id' => $this->vehicle->transport_type_id,
                    'registration_number' => $this->vehicle->registration_number,
                    'registration_plate_letters' => $this->vehicle->registration_plate_letters,
                    'registration_plate_numbers' => $this->vehicle->registration_plate_numbers,
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
            'documents' => $this->whenLoaded('mediaFiles', function () {
                return collect(RepresentativeMediaFileResource::collection($this->mediaFiles->where('collection_name', 'representative_documents'))->resolve())
                    ->groupBy('document_type')
                    ->map(function ($items, $documentType) {
                        return [
                            'document_type' => $documentType,
                            'label' => match ($documentType) {
                                RepresentativeDocumentType::PERSONAL_PHOTO->value => 'Personal Photo',
                                RepresentativeDocumentType::NATIONAL_ID_FRONT->value => 'National ID Front',
                                RepresentativeDocumentType::NATIONAL_ID_BACK->value => 'National ID Back',
                                RepresentativeDocumentType::VEHICLE_PHOTO->value => 'Vehicle Photo',
                                RepresentativeDocumentType::DRIVING_LICENSE_FRONT->value => 'Driving License Front',
                                RepresentativeDocumentType::DRIVING_LICENSE_BACK->value => 'Driving License Back',
                                RepresentativeDocumentType::VEHICLE_LICENSE_FRONT->value => 'Vehicle License Front',
                                RepresentativeDocumentType::VEHICLE_LICENSE_BACK->value => 'Vehicle License Back',
                                default => $documentType,
                            },
                            'files' => $items->values(),
                        ];
                    })
                    ->values();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
