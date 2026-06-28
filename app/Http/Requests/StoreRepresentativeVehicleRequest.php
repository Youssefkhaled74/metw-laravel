<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepresentativeVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'representative_id' => ['required', 'integer', 'exists:representatives,id', 'unique:representative_vehicles,representative_id'],
            'transport_type_id' => ['nullable', 'integer', 'exists:transport_types,id'],
            'registration_number' => ['nullable', 'string', 'max:100', 'unique:representative_vehicles,registration_number'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'manufacture_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'max_volume' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
