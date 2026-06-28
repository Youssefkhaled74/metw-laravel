<?php

namespace App\Http\Requests;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Enum\RepresentativeWorkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $representativeId = $this->route('representative')?->id ?? $this->route('representative');

        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id', Rule::unique('representatives', 'user_id')->ignore($representativeId)],
            'warehouse_id' => [
                'nullable',
                'integer',
                'exists:warehouses,id',
                Rule::requiredIf(fn () => $this->input('account_type') === RepresentativeAccountType::WAREHOUSE->value),
            ],
            'account_type' => ['sometimes', Rule::enum(RepresentativeAccountType::class)],
            'status' => ['sometimes', Rule::enum(RepresentativeStatus::class)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'rejection_reason' => ['sometimes', 'nullable', 'string'],
            'submitted_at' => ['sometimes', 'nullable', 'date'],
            'reviewed_at' => ['sometimes', 'nullable', 'date'],
            'approved_at' => ['sometimes', 'nullable', 'date'],
            'suspended_at' => ['sometimes', 'nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'metadata' => ['sometimes', 'nullable', 'array'],

            'work_types' => ['sometimes', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::enum(RepresentativeWorkType::class), 'distinct'],

            'service_governorate_ids' => ['sometimes', 'array', 'min:1'],
            'service_governorate_ids.*' => ['required', 'integer', 'exists:governorates,id', 'distinct'],

            'service_city_ids' => ['sometimes', 'array', 'min:1'],
            'service_city_ids.*' => ['required', 'integer', 'exists:cities,id', 'distinct'],
        ];
    }
}
