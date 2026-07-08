<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/'],
            'otp' => ['sometimes', 'required', 'digits:4'],
            'purpose' => ['sometimes', 'required', Rule::in(['forgot_password', 'phone_verification', 'registration'])],
        ];
    }
}
