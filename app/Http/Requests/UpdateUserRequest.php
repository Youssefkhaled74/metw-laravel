<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username'=> 'sometimes|string|max:255',
            'email'=> 'nullable|email|unique:users,email,'.$this->user()->id,
            'phone'=> 'sometimes|string|max:30|unique:users,phone,'.$this->user()->id,
            'country_code'=> 'sometimes|string|max:10',
            'password'=> 'sometimes|string|min:8',
            'image'=> 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_remove'=> 'sometimes|boolean',
        ];
    }
}
