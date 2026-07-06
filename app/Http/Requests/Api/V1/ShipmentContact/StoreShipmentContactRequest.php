<?php

namespace App\Http\Requests\Api\V1\ShipmentContact;

use App\Enum\ShipmentContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShipmentContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ShipmentContactType::class)],
            'contact_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('shipment_contacts', 'contact_number')->where(
                    fn ($query) => $query->where('user_id', $this->user()->id)
                ),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'primary_mobile' => ['required', 'string', 'max:30'],
            'secondary_mobile' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'array'],
            'address.label' => ['nullable', 'string', 'max:255'],
            'address.contact_name' => ['nullable', 'string', 'max:255'],
            'address.contact_phone' => ['nullable', 'string', 'max:30'],
            'address.governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
            'address.city_id' => ['required', 'integer', 'exists:cities,id'],
            'address.state_id' => ['nullable', 'integer', 'exists:states,id'],
            'address.country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'address.zone_id' => ['nullable', 'integer', 'exists:zones,id'],
            'address.postal_code' => ['nullable', 'string', 'max:30'],
            'address.address_line_1' => ['nullable', 'string', 'max:255'],
            'address.address_line_2' => ['nullable', 'string', 'max:255'],
            'address.generated_address_number' => ['nullable', 'string', 'max:50'],
            'address.address_name' => ['nullable', 'string', 'max:255'],
            'address.district_or_village_name' => ['required', 'string', 'max:255'],
            'address.district_or_village_type' => ['required', 'in:district,village'],
            'address.street_name' => ['nullable', 'string', 'max:255'],
            'address.branch_from_street' => ['nullable', 'string', 'max:255'],
            'address.building_number' => ['nullable', 'integer', 'min:0'],
            'address.floor_number' => ['nullable', 'integer', 'min:0'],
            'address.building_name' => ['nullable', 'string', 'max:255'],
            'address.nearby_landmark' => ['nullable', 'string', 'max:255'],
            'address.address_description' => ['nullable', 'string', 'max:1000'],
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
