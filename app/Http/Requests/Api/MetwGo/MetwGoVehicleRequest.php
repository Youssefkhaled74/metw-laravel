<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;

class MetwGoVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transport_type_id' => ['required', 'integer', 'exists:transport_types,id'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'vehicle_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}
