<?php

namespace App\Http\Requests;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Enum\RepresentativeWorkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:representatives,user_id'],
            'warehouse_id' => [
                'nullable',
                'integer',
                'exists:warehouses,id',
                Rule::requiredIf(fn () => $this->input('account_type') === RepresentativeAccountType::WAREHOUSE->value),
            ],
            'account_type' => ['required', Rule::enum(RepresentativeAccountType::class)],
            'status' => ['nullable', Rule::enum(RepresentativeStatus::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'string'],
            'submitted_at' => ['nullable', 'date'],
            'reviewed_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'suspended_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],

            'work_types' => ['nullable', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::enum(RepresentativeWorkType::class), 'distinct'],

            'service_governorate_ids' => ['nullable', 'array', 'min:1'],
            'service_governorate_ids.*' => ['required', 'integer', 'exists:governorates,id', 'distinct'],

            'service_city_ids' => ['nullable', 'array', 'min:1'],
            'service_city_ids.*' => ['required', 'integer', 'exists:cities,id', 'distinct'],
        ];
    }
}
