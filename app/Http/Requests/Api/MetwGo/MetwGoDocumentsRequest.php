<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;

class MetwGoDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
