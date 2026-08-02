<?php

namespace App\Enum;

enum ShippingType: string
{
    case DIRECT_ONLY = 'direct_only';
    case DIRECT_AND_MULTI = 'direct_and_multi';

    public static function values(): array
    {
        return array_map(
            static fn (self $type) => $type->value,
            self::cases()
        );
    }

    /**
     * Apply the most restrictive shipping type across a set of sub-classifications.
     * A "direct only" package can never travel a multi-governorate path,
     * so any direct-only package forces the whole shipment to direct-only.
     *
     * @param  array<int, string>  $shippingTypes
     */
    public static function mostRestrictive(array $shippingTypes): self
    {
        if (in_array(self::DIRECT_ONLY->value, $shippingTypes, true)) {
            return self::DIRECT_ONLY;
        }

        return self::DIRECT_AND_MULTI;
    }
}
