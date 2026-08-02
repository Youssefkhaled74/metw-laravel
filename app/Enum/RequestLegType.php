<?php

namespace App\Enum;

enum RequestLegType: string
{
    case DIRECT_DELIVERY = 'direct_delivery';
    case DIRECT_SHIPPING = 'direct_shipping';
    case DELIVERY_TO_WAREHOUSE = 'delivery_to_warehouse';
    case WAREHOUSE_TO_WAREHOUSE = 'warehouse_to_warehouse';
    case DELIVERY_FROM_WAREHOUSE = 'delivery_from_warehouse';
    case DELIVERY_TO_BUS = 'delivery_to_bus';
    case BUS_SEGMENT = 'bus_segment';
    case DELIVERY_FROM_BUS = 'delivery_from_bus';

    /**
     * Which representative work type a courier must hold to be eligible
     * for this leg (see "ملحق ملاحظات 5" section 1).
     */
    public function requiredWorkType(): string
    {
        return match ($this) {
            self::DIRECT_DELIVERY,
            self::DELIVERY_TO_WAREHOUSE,
            self::DELIVERY_FROM_WAREHOUSE,
            self::DELIVERY_TO_BUS,
            self::DELIVERY_FROM_BUS => 'local_delivery',

            self::DIRECT_SHIPPING,
            self::WAREHOUSE_TO_WAREHOUSE => 'inter_governorate_shipping',

            self::BUS_SEGMENT => 'bus_driver',
        };
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $leg) => $leg->value,
            self::cases()
        );
    }
}
