<?php

namespace App\Http\Requests;

use App\Enum\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
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
            'cart_id' => ['required', 'exists:carts,id'],
            // 'payment_method' => ['required', 'string', Rule::in(PaymentMethod::cases())],
            'promo_code' => ['nullable', 'string', 'max:50'],
            // 'partial_amount' => ['required_if:payment_method,' . PaymentMethod::PARTIAL->value, 'numeric'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cart_id.required' => 'Cart ID is required',
            'cart_id.exists' => 'The specified cart does not exist',
            'promo_code.max' => 'Promo code cannot exceed 50 characters',
            'notes.max' => 'Notes cannot exceed 500 characters',
        ];
    }
}
