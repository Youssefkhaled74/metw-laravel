<?php

namespace App\Enum;

enum CourierRole: string
{
    case DELIVERY_COURIER = 'delivery_courier';
    case SHIPPING_COURIER = 'shipping_courier';
    case BUS_DRIVER = 'bus_driver';
    case SHIPPING_AND_DELIVERY_COURIER = 'shipping_and_delivery_courier';

    /**
     * Resolve the courier role(s) from the representative work-type codes
     * (see "ملحق ملاحظات 5" section 1 — Courier Roles).
     *
     * @param  array<int, string>  $workTypes
     * @return array<int, self>
     */
    public static function fromWorkTypes(array $workTypes): array
    {
        $workTypes = array_values(array_filter($workTypes));

        $roles = [];

        if (in_array('bus_driver', $workTypes, true)) {
            $roles[] = self::BUS_DRIVER;
        }

        $hasDelivery = in_array('local_delivery', $workTypes, true);
        $hasShipping = in_array('inter_governorate_shipping', $workTypes, true);

        if ($hasDelivery && $hasShipping) {
            $roles[] = self::SHIPPING_AND_DELIVERY_COURIER;
        } elseif ($hasDelivery) {
            $roles[] = self::DELIVERY_COURIER;
        } elseif ($hasShipping) {
            $roles[] = self::SHIPPING_COURIER;
        }

        return $roles;
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $role) => $role->value,
            self::cases()
        );
    }
}
