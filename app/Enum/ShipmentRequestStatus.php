<?php

namespace App\Enum;

enum ShipmentRequestStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case ASSIGNED = 'assigned';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases()
        );
    }
}
