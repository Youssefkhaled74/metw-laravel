<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEcommerceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_address_id'    => 'nullable|exists:user_addresses,id',
            // Address
            'address' => 'nullable|array',
            'address.city_id' => 'nullable|exists:cities,id',
            'address.state_id' => 'nullable|exists:states,id',
            'address.zone_id' => 'nullable|exists:zones,id',
            'address.street_name' => 'nullable|string|max:255',
            'address.building' => 'nullable|string|max:255',
            'address.floor' => 'nullable|string|max:255',
            'address.landmark' => 'nullable|string|max:255',
            'address.address_type' => 'nullable|string|max:255|in:home,work,other',
            'address.latitude' => 'nullable|numeric|max:255',
            'address.longitude' => 'nullable|numeric|max:255',

            // Contact
            'mobile'             => 'nullable|string',

            // Cart & Items
            'cart_id'            => 'required|exists:ecommerce_carts,id',
            'items'              => 'required|array|min:1',
            'items.*.id'         => 'required|exists:ecommerce_cart_items,id',

            // // Payment
            // 'payment_method'     => 'required|in:full,partial',
            // 'paid_amount'        => 'required_if:payment_method,partial|numeric|min:0',

            // // Promo Code
            // 'promo_code'         => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Invalid mobile number format. It should be an Egyptian number like 01xxxxxxxxx',
        ];
    }
}
