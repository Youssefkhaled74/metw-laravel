<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EcommerceOrderPreview extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cart_id' => 'required|exists:ecommerce_carts,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:ecommerce_cart_items,id',
            'promo_code' => 'nullable|string',
            // 'payment_method' => 'nullable|in:full,partial',
            // 'paid_amount' => 'required_if:payment_method,partial|numeric',

        ];
    } 
}
