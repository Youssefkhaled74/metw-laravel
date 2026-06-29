<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReturnPolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'is_returnable' => (bool) $this->is_returnable,
            'return_fee' => $this->return_fee,
            'return_validity' => $this->return_validity,
        ];
    }
}
