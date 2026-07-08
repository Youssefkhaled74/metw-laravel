<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoRegisterStepOneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/', Rule::unique('users', 'phone')],
            'secondary_phone' => ['nullable', 'regex:/^01[0-2,5]{1}[0-9]{8}$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'address_details' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
