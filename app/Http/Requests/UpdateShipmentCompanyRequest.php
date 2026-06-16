<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentCompanyRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255|unique:shipment_companies,phone',
            'email' => 'sometimes|string|max:255|unique:shipment_companies,email',
            'password' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'facebook_url' => 'sometimes|string|max:255',
            'whatsapp_url' => 'sometimes|string|max:255',
            'is_active' => 'nullable|boolean',
            'coverages' => 'sometimes|array',
            'coverages.*.location_id' => 'sometimes|exists:locations,id',
            'coverages.*.pickup_available' => 'sometimes|boolean',
            'coverages.*.delivery_available' => 'sometimes|boolean',
            'coverages.*.eta_min_days' => 'nullable|integer',
            'coverages.*.eta_max_days' => 'nullable|integer',
            'coverages.*.eta_price' => 'nullable|numeric',
            'coverages.*.notes' => 'nullable|string',
        ];
    }
}
