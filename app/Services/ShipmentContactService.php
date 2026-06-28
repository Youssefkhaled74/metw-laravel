<?php

namespace App\Services;

use App\Enum\AddressType;
use App\Enum\ShipmentContactType;
use App\Models\ShipmentContact;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShipmentContactService
{
    public function __construct(
        protected AddressService $addressService
    ) {
    }

    public function listForUser(User $user)
    {
        return ShipmentContact::query()
            ->where('user_id', $user->id)
            ->with($this->relations())
            ->latest()
            ->get();
    }

    public function getForUserOrFail(User $user, int $id): ShipmentContact
    {
        return ShipmentContact::query()
            ->where('user_id', $user->id)
            ->with($this->relations())
            ->findOrFail($id);
    }

    public function create(User $user, array $data): ShipmentContact
    {
        return DB::transaction(function () use ($user, $data) {
            $contact = ShipmentContact::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'contact_number' => $data['contact_number'],
                'full_name' => $data['full_name'],
                'primary_mobile' => $data['primary_mobile'],
                'secondary_mobile' => $data['secondary_mobile'] ?? null,
            ]);

            $addressPayload = $this->buildAddressPayload($data['address'], $contact);
            $this->addressService->create($contact, $addressPayload);

            return $contact->fresh($this->relations());
        });
    }

    public function update(User $user, int $id, array $data): ShipmentContact
    {
        $contact = $this->getForUserOrFail($user, $id);

        return DB::transaction(function () use ($contact, $data) {
            $contact->update([
                'type' => $data['type'] ?? ($contact->type?->value ?? $contact->type),
                'contact_number' => $data['contact_number'] ?? $contact->contact_number,
                'full_name' => $data['full_name'] ?? $contact->full_name,
                'primary_mobile' => $data['primary_mobile'] ?? $contact->primary_mobile,
                'secondary_mobile' => array_key_exists('secondary_mobile', $data)
                    ? $data['secondary_mobile']
                    : $contact->secondary_mobile,
            ]);

            if (array_key_exists('address', $data)) {
                $address = $contact->foundationAddresses()
                    ->where('is_primary', true)
                    ->first() ?? $contact->foundationAddresses()->first();

                $addressPayload = $this->buildAddressPayload($data['address'], $contact);
                $this->addressService->createOrUpdate($contact, $addressPayload, $address);
            } else {
                $address = $contact->foundationAddresses()
                    ->where('is_primary', true)
                    ->first() ?? $contact->foundationAddresses()->first();

                if ($address) {
                    $this->addressService->update($address, [
                        'type' => $this->mapAddressType($contact->type?->value ?? $contact->type),
                        'contact_name' => $contact->full_name,
                        'contact_phone' => $contact->primary_mobile,
                    ]);
                }
            }

            return $contact->fresh($this->relations());
        });
    }

    public function delete(User $user, int $id): void
    {
        $contact = $this->getForUserOrFail($user, $id);

        DB::transaction(function () use ($contact) {
            $contact->foundationAddresses()->delete();
            $contact->delete();
        });
    }

    protected function buildAddressPayload(array $addressData, ShipmentContact $contact): array
    {
        $contactType = $contact->type?->value ?? $contact->type;

        return array_merge($addressData, [
            'type' => $this->mapAddressType($contactType),
            'contact_name' => $addressData['contact_name'] ?? $contact->full_name,
            'contact_phone' => $addressData['contact_phone'] ?? $contact->primary_mobile,
            'is_primary' => $addressData['is_primary'] ?? true,
        ]);
    }

    protected function mapAddressType(string $type): string
    {
        return match ($type) {
            ShipmentContactType::SENDER->value => AddressType::SHIPMENT_SENDER->value,
            ShipmentContactType::RECEIVER->value => AddressType::SHIPMENT_RECEIVER->value,
            default => AddressType::SHIPMENT_RECEIVER->value,
        };
    }

    protected function relations(): array
    {
        return [
            'primaryAddress.country',
            'primaryAddress.state',
            'primaryAddress.governorate',
            'primaryAddress.city',
            'primaryAddress.zone',
            'foundationAddresses.country',
            'foundationAddresses.state',
            'foundationAddresses.governorate',
            'foundationAddresses.city',
            'foundationAddresses.zone',
        ];
    }
}
