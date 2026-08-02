<?php

namespace App\Enum;

enum DispatchState: string
{
    case PENDING = 'pending';
    case DISPATCHED = 'dispatched';
    case NO_MATCH = 'no_match';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_map(
            static fn (self $state) => $state->value,
            self::cases()
        );
    }
}
