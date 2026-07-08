<?php

namespace App\Http\Requests\Api\MetwGo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MetwGoWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['bank_wallet', 'bank_account', 'cash'])],
            'account_reference' => ['required', 'string', 'max:100'],
        ];
    }
}
