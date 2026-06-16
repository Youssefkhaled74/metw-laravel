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
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'zone_id' => 'required|exists:zones,id',
            'street_name' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'address_type' => 'required|string|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'is_default'   => 'nullable|boolean',
        ];
    }
}
