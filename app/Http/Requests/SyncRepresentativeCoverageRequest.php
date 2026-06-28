<?php

namespace App\Http\Requests;

use App\Enum\RepresentativeWorkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncRepresentativeCoverageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_types' => ['nullable', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::enum(RepresentativeWorkType::class), 'distinct'],
            'service_governorate_ids' => ['nullable', 'array', 'min:1'],
            'service_governorate_ids.*' => ['required', 'integer', 'exists:governorates,id', 'distinct'],
            'service_city_ids' => ['nullable', 'array', 'min:1'],
            'service_city_ids.*' => ['required', 'integer', 'exists:cities,id', 'distinct'],
        ];
    }
}
