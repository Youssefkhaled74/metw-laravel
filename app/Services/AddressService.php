<?php

namespace App\Services;

use App\Enum\AddressType;
use App\Models\Address;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddressService
{
    public function create(Model $addressable, array $data): Address
    {
        return DB::transaction(function () use ($addressable, $data) {
            $payload = $this->preparePayload($data);

            if (($payload['is_primary'] ?? false) === true) {
                $this->clearPrimaryFlag($addressable);
            }

            /** @var Address $address */
            $address = $addressable->foundationAddresses()->create($payload);

            return $address->load(['country', 'state', 'governorate', 'city', 'zone']);
        });
    }

    public function update(Address $address, array $data): Address
    {
        return DB::transaction(function () use ($address, $data) {
            $payload = $this->preparePayload($data, $address);

            if (($payload['is_primary'] ?? false) === true) {
                $this->clearPrimaryFlag($address->addressable, $address->id);
            }

            $address->update($payload);

            return $address->fresh(['country', 'state', 'governorate', 'city', 'zone']);
        });
    }

    public function createOrUpdate(Model $addressable, array $data, ?Address $address = null): Address
    {
        if ($address !== null) {
            $this->ensureOwnership($addressable, $address);

            return $this->update($address, $data);
        }

        return $this->create($addressable, $data);
    }

    public function setPrimary(Address $address): Address
    {
        return DB::transaction(function () use ($address) {
            $this->clearPrimaryFlag($address->addressable, $address->id);

            $address->update(['is_primary' => true]);

            return $address->fresh(['country', 'state', 'governorate', 'city', 'zone']);
        });
    }

    public function validateCityBelongsToGovernorate(?int $cityId, ?int $governorateId): void
    {
        if (! $cityId || ! $governorateId) {
            return;
        }

        $city = City::withoutGlobalScopes()->find($cityId);

        if (! $city) {
            throw ValidationException::withMessages([
                'city_id' => ['The selected city does not exist.'],
            ]);
        }

        if ((int) $city->governorate_id !== (int) $governorateId) {
            throw ValidationException::withMessages([
                'city_id' => ['The selected city does not belong to the selected governorate.'],
            ]);
        }
    }

    protected function preparePayload(array $data, ?Address $address = null): array
    {
        $payload = Arr::only($data, [
            'label',
            'type',
            'contact_name',
            'contact_phone',
            'country_id',
            'state_id',
            'governorate_id',
            'city_id',
            'zone_id',
            'postal_code',
            'address_line_1',
            'address_line_2',
            'street_name',
            'building',
            'floor',
            'landmark',
            'latitude',
            'longitude',
            'is_primary',
            'is_active',
            'metadata',
        ]);

        $payload = $this->normalizeType($payload, $address);
        $payload = $this->normalizeLocation($payload, $address);
        $this->validateType($payload['type'] ?? null);
        $this->validateCityBelongsToGovernorate(
            $payload['city_id'] ?? null,
            $payload['governorate_id'] ?? null
        );

        return $payload;
    }

    protected function normalizeType(array $payload, ?Address $address = null): array
    {
        if (! array_key_exists('type', $payload) && $address) {
            $payload['type'] = $address->type;
        }

        return $payload;
    }

    protected function normalizeLocation(array $payload, ?Address $address = null): array
    {
        $cityId = $payload['city_id'] ?? $address?->city_id;
        $governorateId = $payload['governorate_id'] ?? $address?->governorate_id;

        if ($cityId) {
            $city = City::withoutGlobalScopes()->find($cityId);

            if (! $city) {
                throw ValidationException::withMessages([
                    'city_id' => ['The selected city does not exist.'],
                ]);
            }

            $payload['state_id'] = $payload['state_id'] ?? $address?->state_id ?? $city->state_id;
            $payload['governorate_id'] = $governorateId ?? $city->governorate_id;
        }

        if (! empty($payload['governorate_id'])) {
            $governorate = Governorate::withoutGlobalScopes()->find($payload['governorate_id']);

            if (! $governorate) {
                throw ValidationException::withMessages([
                    'governorate_id' => ['The selected governorate does not exist.'],
                ]);
            }
        }

        return $payload;
    }

    protected function validateType(?string $type): void
    {
        if ($type === null) {
            return;
        }

        if (! in_array($type, AddressType::values(), true)) {
            throw ValidationException::withMessages([
                'type' => ['The selected address type is invalid.'],
            ]);
        }
    }

    protected function clearPrimaryFlag(Model $addressable, ?int $exceptId = null): void
    {
        $query = $addressable->foundationAddresses()->where('is_primary', true);

        if ($exceptId) {
            $query->whereKeyNot($exceptId);
        }

        $query->update(['is_primary' => false]);
    }

    protected function ensureOwnership(Model $addressable, Address $address): void
    {
        if (
            $address->addressable_type !== $addressable::class
            || (int) $address->addressable_id !== (int) $addressable->getKey()
        ) {
            throw ValidationException::withMessages([
                'address' => ['The provided address does not belong to the given model.'],
            ]);
        }
    }
}
