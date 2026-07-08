<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;

class MetwGoServiceAreasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'governorate_ids' => ['required', 'array', 'min:1'],
            'governorate_ids.*' => ['required', 'integer', 'exists:governorates,id'],
            'city_ids' => ['nullable', 'array'],
            'city_ids.*' => ['required', 'integer', 'exists:cities,id'],
            'villages_service_enabled' => ['required', 'boolean'],
        ];
    }
}
