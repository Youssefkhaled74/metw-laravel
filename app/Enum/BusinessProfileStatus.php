<?php

namespace App\Enum;

enum BusinessProfileStatus: string
{
    case INCOMPLETE = 'incomplete';
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases()
        );
    }
}
