<?php

namespace App\Http\Requests;

use App\Enum\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DirectCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'package_id' => ['sometimes', 'integer', 'exists:packages,id'],

            'shipment_company_id' => ['nullable', 'integer', 'exists:shipment_companies,id'],
            'type_id' => ['required', 'integer', 'exists:package_types,id'],
            'size_id' => ['required', 'integer', 'exists:sizes,id'],
            'delivery_type_id' => ['required', 'integer', 'exists:delivery_types,id'],
            'consignment_type_id' => ['required', 'integer', 'exists:consignment_types,id'],

            'pickup_address' => ['required', 'array'],
            'pickup_address.address' => ['required', 'string'],
            'pickup_address.location' => ['nullable', 'string'],
            // 'pickup_address.city' => ['nullable', 'string'],
            // 'pickup_address.state' => ['nullable', 'string'],
            // 'pickup_address.country' => ['nullable', 'string'],
            'pickup_address.country_id' => ['nullable', 'string', 'exists:countries,id'],
            'pickup_address.city_id' => ['nullable', 'string', 'exists:cities,id'],
            'pickup_address.state_id' => ['nullable', 'string', 'exists:states,id'],
            'pickup_address.zone_id' => ['nullable', 'string', 'exists:zones,id'],
            'pickup_address.landmark' => ['nullable', 'string'],
            'pickup_address.phone' => ['required', 'string'],
            'pickup_address.latitude' => ['required', 'string'],
            'pickup_address.longitude' => ['required', 'string'],


            'dropoff_address' => ['required', 'array'],
            'dropoff_address.address' => ['required', 'string'],
            'dropoff_address.location' => ['nullable', 'string'],
            // 'dropoff_address.city' => ['nullable', 'string'],
            // 'dropoff_address.state' => ['nullable', 'string'],
            // 'dropoff_address.country' => ['nullable', 'string'],
            'dropoff_address.country_id' => ['nullable', 'string', 'exists:countries,id'],
            'dropoff_address.city_id' => ['nullable', 'string', 'exists:cities,id'],
            'dropoff_address.state_id' => ['nullable', 'string', 'exists:states,id'],
            'dropoff_address.zone_id' => ['nullable', 'string', 'exists:zones,id'],
            'dropoff_address.landmark' => ['nullable', 'string'],
            'dropoff_address.phone' => ['required', 'string'],
            'dropoff_address.latitude' => ['required', 'string'],
            'dropoff_address.longitude' => ['required', 'string'],


            'details' => ['required', 'array'],
            'details.sender_name' => ['required', 'string'],
            'details.sender_phone' => ['required', 'string'],
            'details.recive_name' => ['required', 'string'],
            'details.recive_phone' => ['required', 'string'],
            'details.pickup_date' => ['nullable', 'date'],
            'details.pickup_time' => ['nullable', 'string'],

            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],

            'est_date' => ['nullable', 'date'],
            'est_price' => ['nullable', 'numeric', 'min:0'],
            'est_days' => ['nullable', 'integer', 'min:0'],

            // 'payment_method' => ['required', 'string', Rule::in(PaymentMethod::cases())],
            // 'partial_amount' => ['required_if:payment_method,' . PaymentMethod::PARTIAL->value, 'numeric', 'min:0'],
            // 'promo_code' => ['nullable', 'string'],

            // Optional split acceptance to create two child orders
            'split' => ['nullable', 'array'],
            'split.accept' => ['required_with:split', 'boolean'],
            'split.selected_suggestion_index' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
