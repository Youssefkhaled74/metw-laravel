<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username'=> 'required|string|max:255',
            'email'=> 'nullable|email|unique:users,email',
            'phone'=> 'required|string|max:30|unique:users,phone',
            'country_code'=> 'required|string|max:10',
            'password'=> 'required|string|min:8|confirmed',
            'fcm_token'=> 'nullable|string|max:255',
            'fcm_token_shipment'=> 'nullable|string|max:255',
            'image'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
