<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvancePaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_path_id' => $this->request_path_id,
            'submitter_type' => $this->submitter_type?->value ?? $this->submitter_type,
            'submitter_id' => $this->submitter_id,
            'amount' => $this->amount !== null ? (float) $this->amount : null,
            'currency' => $this->currency ?? 'EGP',
            'status' => $this->status?->value ?? $this->status,
            'payment_method' => $this->payment_method,
            'reference' => $this->reference,
            'confirmed_at' => $this->confirmed_at,
            'rejected_at' => $this->rejected_at,
            'refunded_at' => $this->refunded_at,
            'notes' => $this->notes,
        ];
    }
}
