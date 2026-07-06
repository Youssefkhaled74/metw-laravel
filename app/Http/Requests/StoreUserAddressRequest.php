<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAddressRequest extends FormRequest
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
            'city_id' => 'required|exists:cities,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
            'zone_id' => 'nullable|exists:zones,id',
            'generated_address_number' => 'nullable|string|max:50',
            'address_name' => 'nullable|string|max:255',
            'district_or_village_name' => 'nullable|string|max:255',
            'district_or_village_type' => 'nullable|in:district,village',
            'street_name' => 'required|string|max:255',
            'branch_from_street' => 'nullable|string|max:255',
            'building_number' => 'nullable|digits_between:1,10',
            'floor_number' => 'nullable|digits_between:1,10',
            'building_name' => 'nullable|string|max:255',
            'nearby_landmark' => 'nullable|string|max:255',
            'address_description' => 'nullable|string|max:1000',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'address_type' => 'required|string|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'is_default'   => 'nullable|boolean',
        ];
    }
}
