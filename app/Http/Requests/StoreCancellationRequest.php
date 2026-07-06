<?php

namespace App\Http\Requests;

use App\Enum\RequestType;
use Illuminate\Validation\Rule;

class StoreCancellationRequest extends StoreReturnRequestRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['request_type'] = ['required', 'string', Rule::in([RequestType::CANCELLATION->value])];

        return $rules;
    }
}
