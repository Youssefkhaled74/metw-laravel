<?php

namespace App\Http\Requests\Api\MetwGo;

use App\Enum\ReturnRequestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rejectionStatuses = [
            ReturnRequestStatus::REJECTED->value,
            ReturnRequestStatus::REJECTED_RETURN->value,
        ];

        return [
            'reason_id' => ['required', 'integer', 'exists:return_reasons,id'],
            'custom_reason_text' => ['nullable', 'string', 'max:1000'],
            'status' => [
                'required',
                Rule::in([
                    ReturnRequestStatus::REQUESTED->value,
                    ReturnRequestStatus::APPROVED->value,
                    ReturnRequestStatus::REJECTED->value,
                    ReturnRequestStatus::RECEIVED_INSPECTING->value,
                    ReturnRequestStatus::ACCEPTED_RETURN->value,
                    ReturnRequestStatus::REJECTED_RETURN->value,
                ]),
            ],
            'rejection_reason' => [
                Rule::requiredIf(fn () => in_array($this->input('status'), $rejectionStatuses)),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
