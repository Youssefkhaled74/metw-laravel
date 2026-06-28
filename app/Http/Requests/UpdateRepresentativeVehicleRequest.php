<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepresentativeVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id ?? $this->route('vehicle');

        return [
            'representative_id' => ['sometimes', 'integer', 'exists:representatives,id', Rule::unique('representative_vehicles', 'representative_id')->ignore($vehicleId)],
            'transport_type_id' => ['sometimes', 'nullable', 'integer', 'exists:transport_types,id'],
            'registration_number' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('representative_vehicles', 'registration_number')->ignore($vehicleId)],
            'license_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'brand' => ['sometimes', 'nullable', 'string', 'max:100'],
            'model' => ['sometimes', 'nullable', 'string', 'max:100'],
            'color' => ['sometimes', 'nullable', 'string', 'max:50'],
            'manufacture_year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'max_weight' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_volume' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
