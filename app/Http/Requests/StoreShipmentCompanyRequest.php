<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentCompanyRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:shipment_companies,phone',
            'email' => 'required|string|max:255|unique:shipment_companies,email',
            'password' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'facebook_url' => 'nullable|string|max:255',
            'whatsapp_url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'coverages' => 'required|array',
            'coverages.*.location_id' => 'required|exists:locations,id',
            'coverages.*.pickup_available' => 'required|boolean',
            'coverages.*.delivery_available' => 'required|boolean',
            'coverages.*.eta_min_days' => 'nullable|integer',
            'coverages.*.eta_max_days' => 'nullable|integer',
            'coverages.*.eta_price' => 'nullable|numeric',
            'coverages.*.notes' => 'nullable|string',
        ];
    }
}
