<?php

namespace App\Http\Requests\Api\V1\ShipmentRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequestPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_name' => ['required', 'string', 'max:255'],
            'package_type' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }
}
