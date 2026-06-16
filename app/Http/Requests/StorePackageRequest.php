<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => ['nullable', 'integer', 'exists:package_types,id'],
            'size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'shipment_company_id' => ['required', 'integer', 'exists:shipment_companies,id'],
            'delivery_type_id' => ['nullable', 'integer', 'exists:delivery_types,id'],
            'consignment_type_id' => ['nullable', 'integer', 'exists:consignment_types,id'],

            'pickup_address' => ['required', 'array'],
            'pickup_address.address' => ['required', 'string'],
            'pickup_address.phone' => ['required', 'string'],

            'dropoff_address' => ['required', 'array'],
            'dropoff_address.address' => ['required', 'string'],
            'dropoff_address.phone' => ['required', 'string'],

            'details' => ['required', 'array'],
            'details.sender_name' => ['required', 'string'],
            'details.sender_phone' => ['required', 'string'],
            'details.recive_name' => ['required', 'string'],
            'details.recive_phone' => ['required', 'string'],
            'details.pickup_date' => ['nullable', 'date'],
            'details.pickup_time' => ['nullable', 'string'],

            'images' => ['sometimes', 'array'],
        ];
    }
}
