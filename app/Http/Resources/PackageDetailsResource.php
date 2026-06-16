<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_name' => $this->sender_name,
            'sender_phone' => $this->sender_phone,
            'recive_name' => $this->recive_name,
            'recive_phone' => $this->recive_phone,
            'pickup_date' => $this->pickup_date,
            'pickup_time' => $this->pickup_time,
        ];
    }
}
