<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'sometimes|string',
            'email'=>'sometimes|email|unique:vendors,email',
            'country_code'=>'sometimes|string',
            'phone'=>'sometimes|unique:vendors,phone',
            'password'=>'sometimes|string',
            'address'=>'sometimes|string',
            'latitude'=>'nullable|numeric',
            'longitude'=>'nullable|numeric',
            'logo'=>'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email_verified'=>'nullable|boolean',
            'phone_verified'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
            'fcm_token'=>'nullable',
        ];
    }
}
