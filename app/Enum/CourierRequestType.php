<?php

namespace App\Enum;

/**
 * The three automatic request types for inter-governorate requests
 * (Section 2) plus the intra-governorate fast delivery (Section 4).
 */
enum CourierRequestType: string
{
    /** Type 1 – Single-path (Direct Shipping only). */
    case SINGLE_PATH = 'single_path';

    /** Type 2 – Dual-path (Direct Shipping + Warehouses). */
    case DUAL_PATH = 'dual_path';

    /** Type 3 – Triple-path (Direct Shipping + Warehouses + Bus). */
    case TRIPLE_PATH = 'triple_path';

    /** Intra-governorate fast delivery (Section 4). */
    case FAST_DELIVERY = 'fast_delivery';

    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    /**
     * The parallel paths sent for each request type (Sections 2 & 4).
     *
     * @return array<int, RequestPathType>
     */
    public function paths(): array
    {
        return match ($this) {
            self::SINGLE_PATH => [RequestPathType::DIRECT_SHIPPING],
            self::DUAL_PATH => [RequestPathType::DIRECT_SHIPPING, RequestPathType::WAREHOUSES],
            self::TRIPLE_PATH => [RequestPathType::DIRECT_SHIPPING, RequestPathType::WAREHOUSES, RequestPathType::BUS],
            self::FAST_DELIVERY => [RequestPathType::FAST_DELIVERY],
        };
    }
}
