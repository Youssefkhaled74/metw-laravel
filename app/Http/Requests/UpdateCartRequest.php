<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_company_id' => ['nullable', 'exists:shipment_companies,id'],
            'selected_suggestion_index' => ['nullable', 'integer', 'min:0'],

            // Packages - allow full package update
            'packages' => ['nullable', 'array', 'min:1'],
            'packages.*.id' => ['required', 'integer', 'exists:packages,id'],
            'packages.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'packages.*.sub_category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'packages.*.weight' => ['nullable', 'numeric', 'min:0.1'],
            'packages.*.type_id' => ['nullable', 'integer', 'exists:package_types,id'],
            'packages.*.size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'packages.*.delivery_type_id' => ['nullable', 'integer', 'exists:delivery_types,id'],
            'packages.*.consignment_type_id' => ['nullable', 'integer', 'exists:consignment_types,id'],

            // Pickup & Dropoff with village flag
            'pickup_address' => ['nullable', 'array'],
            'pickup_address.address' => ['nullable', 'string'],
            'pickup_address.phone' => ['nullable', 'string'],
            'pickup_address.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'pickup_address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'pickup_address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'pickup_address.latitude' => ['nullable', 'string'],
            'pickup_address.longitude' => ['nullable', 'string'],
            'pickup_address.is_saved' => ['nullable', 'boolean'],
            'pickup_address.is_village' => ['nullable', 'boolean'],

            'dropoff_address' => ['nullable', 'array'],
            'dropoff_address.address' => ['nullable', 'string'],
            'dropoff_address.phone' => ['nullable', 'string'],
            'dropoff_address.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'dropoff_address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'dropoff_address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'dropoff_address.latitude' => ['nullable', 'string'],
            'dropoff_address.longitude' => ['nullable', 'string'],
            'dropoff_address.is_saved' => ['nullable', 'boolean'],
            'dropoff_address.is_village' => ['nullable', 'boolean'],

            // Details
            'details' => ['nullable', 'array'],
            'details.sender_name' => ['nullable', 'string'],
            'details.sender_phone' => ['nullable', 'string'],
            'details.recive_name' => ['nullable', 'string'],
            'details.recive_phone' => ['nullable', 'string'],
            'details.pickup_date' => ['nullable', 'date'],
            'details.pickup_time' => ['nullable', 'string'],

            'est_date' => ['nullable', 'date'],
            'est_days' => ['nullable', 'integer', 'min:0'],
            'est_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
