<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierCategory;
use App\Enum\ShippingType;

/**
 * Immutable profile describing the matching criteria of a request that is
 * about to be distributed to couriers (see "ملحق ملاحظات 5" section 2).
 */
class CourierRequestProfile
{
    public function __construct(
        public readonly ?int $originGovernorateId,
        public readonly ?int $originCityId,
        public readonly ?int $destinationGovernorateId,
        public readonly ?int $destinationCityId,
        public readonly float $weight,
        public readonly float $volume,
        public readonly bool $hasVillage,
        public readonly ShippingType $shippingType,
        public readonly ?CourierCategory $courierCategory,
    ) {}

    public function isInterGovernorate(): bool
    {
        return $this->originGovernorateId
            && $this->destinationGovernorateId
            && $this->originGovernorateId !== $this->destinationGovernorateId;
    }

    public function toArray(): array
    {
        return [
            'origin_governorate_id' => $this->originGovernorateId,
            'origin_city_id' => $this->originCityId,
            'destination_governorate_id' => $this->destinationGovernorateId,
            'destination_city_id' => $this->destinationCityId,
            'total_weight' => $this->weight,
            'total_volume' => $this->volume,
            'has_village' => $this->hasVillage,
            'shipping_type' => $this->shippingType->value,
            'courier_category' => $this->courierCategory?->value,
        ];
    }
}
