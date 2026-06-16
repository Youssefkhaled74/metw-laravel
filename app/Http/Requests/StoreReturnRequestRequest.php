<?php

namespace App\Http\Requests;

use App\Enum\ReturnReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequestRequest extends FormRequest
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
            'order_id' => 'required|exists:ecommerce_orders,id',
            'refund_type' => 'nullable|in:wallet,cash',
            'reason' => 'required|string',

            'cancel_reason_ids' => 'required|array|min:1',
            'cancel_reason_ids.*' => [
                'integer',
                Rule::exists('cancel_reasons', 'id')->where('is_active', true),
            ],
            // 'other_reason' => 'nullable|string|max:500',
            // 'notes' => 'nullable|string|max:1000',
            // 'pickup_address_id' => 'nullable|exists:user_addresses,id',
            // 'pickup_date' => 'nullable|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:ecommerce_order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:500',
        ];
    }
}
