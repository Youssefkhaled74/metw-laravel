<?php

namespace App\Enum;

enum AddressType: string
{
    case USER_DELIVERY = 'user_delivery';
    case VENDOR_BRANCH = 'vendor_branch';
    case WAREHOUSE_LOCATION = 'warehouse_location';
    case SHIPMENT_SENDER = 'shipment_sender';
    case SHIPMENT_RECEIVER = 'shipment_receiver';
    case REPRESENTATIVE_HOME = 'representative_home';

    public static function values(): array
    {
        return array_map(
            static fn (self $type) => $type->value,
            self::cases()
        );
    }
}
