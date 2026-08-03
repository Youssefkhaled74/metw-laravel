<?php

namespace App\Enum;

enum CourierAssignmentStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';

    /** System auto-approved this courier for its leg (best among acceptors). */
    case CONFIRMED = 'confirmed';

    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases()
        );
    }
}
