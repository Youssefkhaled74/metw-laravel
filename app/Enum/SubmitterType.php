<?php

namespace App\Enum;

/**
 * Who created the request (Section 3, Common Rules):
 * a User from the Shipping App or a Seller from the Market App.
 */
enum SubmitterType: string
{
    case USER = 'user';

    case SELLER = 'seller';

    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }
}
