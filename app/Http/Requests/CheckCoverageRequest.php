<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckCoverageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_company_id' => ['required', 'integer', 'exists:shipment_companies,id'],

            'pickup_address' => ['required', 'array'],
            'pickup_address.address' => ['nullable', 'string'],
            'pickup_address.location' => ['nullable', 'string'],
            'pickup_address.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'pickup_address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'pickup_address.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'pickup_address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'pickup_address.latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup_address.longitude' => ['required', 'numeric', 'between:-180,180'],

            'dropoff_address' => ['required', 'array'],
            'dropoff_address.address' => ['nullable', 'string'],
            'dropoff_address.location' => ['nullable', 'string'],
            'dropoff_address.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'dropoff_address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'dropoff_address.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'dropoff_address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'dropoff_address.latitude' => ['required', 'numeric', 'between:-90,90'],
            'dropoff_address.longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
