<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAddressRequest extends FormRequest
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
            'city_id' => 'sometimes|exists:cities,id',
            'governorate_id' => 'sometimes|nullable|exists:governorates,id',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'zone_id' => 'nullable|exists:zones,id',
            'generated_address_number' => 'sometimes|string|max:50',
            'address_name' => 'sometimes|nullable|string|max:255',
            'district_or_village_name' => 'sometimes|nullable|string|max:255',
            'district_or_village_type' => 'sometimes|nullable|in:district,village',
            'street_name' => 'sometimes|string|max:255',
            'branch_from_street' => 'sometimes|nullable|string|max:255',
            'building_number' => 'sometimes|nullable|digits_between:1,10',
            'floor_number' => 'sometimes|nullable|digits_between:1,10',
            'building_name' => 'sometimes|nullable|string|max:255',
            'nearby_landmark' => 'sometimes|nullable|string|max:255',
            'address_description' => 'sometimes|nullable|string|max:1000',
            'building' => 'sometimes|nullable|string|max:255',
            'floor' => 'sometimes|nullable|string|max:255',
            'landmark' => 'sometimes|nullable|string|max:255',
            'address_type' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|max:255',
            'longitude' => 'sometimes|numeric|max:255',
            'is_default'   => 'nullable|boolean',
        ];
    }
}
