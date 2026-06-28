<?php

namespace App\Enum;

enum ShipmentContactType: string
{
    case SENDER = 'sender';
    case RECEIVER = 'receiver';

    public static function values(): array
    {
        return array_map(
            static fn (self $type) => $type->value,
            self::cases()
        );
    }
}
