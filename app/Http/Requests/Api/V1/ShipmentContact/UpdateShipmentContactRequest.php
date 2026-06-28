<?php

namespace App\Http\Requests\Api\V1\ShipmentContact;

use App\Enum\ShipmentContactType;
use App\Models\ShipmentContact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var ShipmentContact|null $shipmentContact */
        $shipmentContact = ShipmentContact::find($this->route('id'));

        return [
            'type' => ['sometimes', Rule::enum(ShipmentContactType::class)],
            'contact_number' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('shipment_contacts', 'contact_number')
                    ->ignore($shipmentContact?->id)
                    ->where(fn ($query) => $query->where('user_id', $this->user()->id)),
            ],
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'primary_mobile' => ['sometimes', 'required', 'string', 'max:30'],
            'secondary_mobile' => ['sometimes', 'nullable', 'string', 'max:30'],
            'address' => ['sometimes', 'array'],
            'address.label' => ['nullable', 'string', 'max:255'],
            'address.contact_name' => ['nullable', 'string', 'max:255'],
            'address.contact_phone' => ['nullable', 'string', 'max:30'],
            'address.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'address.governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
            'address.city_id' => ['sometimes', 'required', 'integer', 'exists:cities,id'],
            'address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'address.postal_code' => ['nullable', 'string', 'max:30'],
            'address.address_line_1' => ['nullable', 'string', 'max:255'],
            'address.address_line_2' => ['nullable', 'string', 'max:255'],
            'address.street_name' => ['nullable', 'string', 'max:255'],
            'address.building' => ['nullable', 'string', 'max:255'],
            'address.floor' => ['nullable', 'string', 'max:255'],
            'address.landmark' => ['nullable', 'string', 'max:255'],
            'address.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'address.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address.is_primary' => ['nullable', 'boolean'],
            'address.is_active' => ['nullable', 'boolean'],
            'address.metadata' => ['nullable', 'array'],
        ];
    }
}
