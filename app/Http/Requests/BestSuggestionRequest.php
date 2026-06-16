<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BestSuggestionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'preferred_company_id' => ['nullable', 'exists:shipment_companies,id'],

            // Packages array similar to AddToCartRequest
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.category_id' => ['required', 'exists:main_categories,id'],
            'packages.*.sub_category_id' => ['required', 'exists:categories,id'],
            'packages.*.weight' => ['required', 'numeric', 'min:0.1'],
            'packages.*.size' => ['required', 'numeric', 'min:0.1'], // size in cm or whatever unit
            'packages.*.piece' => ['required', 'integer', 'min:1'],
            // Single package fields for backward compatibility
            'category_ids' => ['sometimes', 'array', 'min:1'],
            'category_ids.*' => ['exists:main_categories,id'],
            'sub_category_ids' => ['sometimes', 'array', 'min:1'],
            'sub_category_ids.*' => ['exists:categories,id'],
            'size_id' => ['sometimes', 'exists:sizes,id'],
            'weight' => ['sometimes', 'numeric', 'min:0.1'],

            'pickup_address.zone_id' => ['required', 'exists:zones,id'],
            'pickup_address.longitude' => ['required', 'numeric', 'between:-180,180'],
            'pickup_address.latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup_address.city_id' => ['required', 'exists:cities,id'],
            'pickup_address.state_id' => ['required', 'exists:states,id'],
            'pickup_address.is_village' => ['required', 'boolean'],

            'dropoff_address.longitude' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_address.latitude' => ['required', 'numeric', 'between:-90,90'],
            'dropoff_address.zone_id' => ['required', 'exists:zones,id'],
            'dropoff_address.city_id' => ['required', 'exists:cities,id'],
            'dropoff_address.state_id' => ['required', 'exists:states,id'],
            'dropoff_address.is_village' => ['required', 'boolean'],
        ];
    }
}
