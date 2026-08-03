<?php

namespace App\Enum;

/**
 * The four path types summarised in Section 5.
 */
enum RequestPathType: string
{
    /** Path 1: Sender → Shipping Courier → Receiver. */
    case DIRECT_SHIPPING = 'direct_shipping';

    /** Path 3: Sender → Delivery → Warehouse → Shipping → Warehouse → Delivery → Receiver. */
    case WAREHOUSES = 'warehouses';

    /** Path 2: Sender → Delivery → Bus Driver → Delivery → Receiver. */
    case BUS = 'bus';

    /** Path 4 (same governorate): Sender → Delivery Courier → Receiver. */
    case FAST_DELIVERY = 'fast_delivery';

    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    /**
     * The leg types that belong to this path (Section 5).
     *
     * @return array<int, RequestLegType>
     */
    public function legs(): array
    {
        return match ($this) {
            self::DIRECT_SHIPPING => [RequestLegType::DIRECT_SHIPPING],
            self::WAREHOUSES => [
                RequestLegType::DELIVERY_TO_WAREHOUSE,
                RequestLegType::WAREHOUSE_TO_WAREHOUSE,
                RequestLegType::DELIVERY_FROM_WAREHOUSE,
            ],
            self::BUS => [
                RequestLegType::DELIVERY_TO_BUS,
                RequestLegType::BUS_SEGMENT,
                RequestLegType::DELIVERY_FROM_BUS,
            ],
            self::FAST_DELIVERY => [RequestLegType::DIRECT_DELIVERY],
        };
    }
}
