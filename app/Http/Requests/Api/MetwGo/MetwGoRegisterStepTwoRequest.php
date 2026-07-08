<?php

namespace App\Http\Requests\Api\MetwGo;

use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoRegisterStepTwoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'courier_type' => ['required', Rule::in(['freelance', 'warehouse'])],
            'warehouse_id' => [
                'nullable',
                'integer',
                'exists:warehouses,id',
                Rule::requiredIf(fn () => $this->input('courier_type') === 'warehouse'),
            ],
            'work_types' => ['required', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::in(app(MetwGoCourierService::class)->workTypeValidationCodes())],
        ];
    }
}
