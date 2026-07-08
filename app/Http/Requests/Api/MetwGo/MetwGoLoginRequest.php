<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;

class MetwGoLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^01[0-2,5]{1}[0-9]{8}$/'],
            'password' => ['required', 'string', 'min:8'],
            'device_token' => ['nullable', 'string', 'max:500'],
        ];
    }
}
