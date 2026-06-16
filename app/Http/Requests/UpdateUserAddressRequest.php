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
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'sometimes|exists:states,id',
            'zone_id' => 'sometimes|exists:zones,id',
            'street_name' => 'sometimes|string|max:255',
            'building' => 'sometimes|string|max:255',
            'floor' => 'sometimes|string|max:255',
            'landmark' => 'sometimes|string|max:255',
            'address_type' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|max:255',
            'longitude' => 'sometimes|numeric|max:255',
            'is_default'   => 'nullable|boolean',
        ];
    }
}
