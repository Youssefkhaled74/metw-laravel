<?php

namespace App\Enum;

enum AddressLocalityType: string
{
    case DISTRICT = 'district';
    case VILLAGE = 'village';

    public static function values(): array
    {
        return array_map(
            static fn (self $type) => $type->value,
            self::cases()
        );
    }
}
