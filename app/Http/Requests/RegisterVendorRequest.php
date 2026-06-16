<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVendorRequest extends FormRequest
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
            'name'=>'required|string',
            'email'=>'required|email|unique:vendors,email',
            'country_code'=>'required|string',
            'phone'=>'required|unique:vendors,phone',
            'password'=>'required|string',
            'address'=>'required|string',
            'latitude'=>'nullable|numeric',
            'longitude'=>'nullable|numeric',
            'logo'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email_verified'=>'nullable|boolean',
            'phone_verified'=>'nullable|boolean',
            'is_active'=>'nullable|boolean',
            'fcm_token'=>'nullable',
        ];
    }
}
