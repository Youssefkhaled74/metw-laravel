<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['online', 'offline'])],
        ];
    }
}
