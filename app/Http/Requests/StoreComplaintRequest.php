<?php

namespace App\Http\Requests;

use App\Enum\ComplaintType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_type' => ['required', 'string', Rule::in(array_column(ComplaintType::cases(), 'value'))],
            'complaintable_type' => 'required|string|max:255',
            'complaintable_id' => 'required|integer',
            'subject' => 'nullable|string|max:255',
            'description' => 'required|string',
            'reason' => 'nullable|string|max:1000',
        ];
    }
}
