<?php

namespace App\Http\Requests\Api\V1\ShipmentRequest;

use App\Enum\ShipmentContactType;
use App\Models\ShipmentContact;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_contact_id' => ['required', 'integer', 'exists:shipment_contacts,id'],
            'receiver_contact_id' => ['required', 'integer', 'exists:shipment_contacts,id', 'different:sender_contact_id'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userId = $this->user()->id;

            $sender = ShipmentContact::where('user_id', $userId)
                ->where('type', ShipmentContactType::SENDER->value)
                ->find($this->input('sender_contact_id'));

            if (! $sender) {
                $validator->errors()->add('sender_contact_id', 'The selected sender contact is invalid.');
            }

            $receiver = ShipmentContact::where('user_id', $userId)
                ->where('type', ShipmentContactType::RECEIVER->value)
                ->find($this->input('receiver_contact_id'));

            if (! $receiver) {
                $validator->errors()->add('receiver_contact_id', 'The selected receiver contact is invalid.');
            }
        });
    }
}
