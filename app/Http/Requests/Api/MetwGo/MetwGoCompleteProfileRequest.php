<?php

namespace App\Http\Requests\Api\MetwGo;

use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoCompleteProfileRequest extends FormRequest
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

            'transport_type_id' => ['required', 'integer', 'exists:transport_types,id'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'vehicle_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            'governorate_ids' => ['required', 'array', 'min:1'],
            'governorate_ids.*' => ['required', 'integer', 'exists:governorates,id'],
            'city_ids' => ['nullable', 'array'],
            'city_ids.*' => ['required', 'integer', 'exists:cities,id'],
            'villages_service_enabled' => ['required', 'boolean'],

            'profile_photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'national_id_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'national_id_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'driving_license_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'driving_license_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'vehicle_license_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'vehicle_license_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
