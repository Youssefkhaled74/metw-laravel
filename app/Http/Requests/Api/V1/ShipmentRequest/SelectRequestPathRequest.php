<?php

namespace App\Http\Requests\Api\V1\ShipmentRequest;

use Illuminate\Foundation\Http\FormRequest;

class SelectRequestPathRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_path_id' => ['required', 'integer', 'exists:request_paths,id'],
        ];
    }
}
