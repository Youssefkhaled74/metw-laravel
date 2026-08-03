<?php

namespace App\Enum;

enum AdvancePaymentStatus: string
{
    /** Submitter has been asked to pay the advance. */
    case PENDING = 'pending';

    /** Submitter submitted / paid the advance (awaiting admin confirmation for Users). */
    case PAID = 'paid';

    /** Admin confirmed the payment – official order to start execution. */
    case CONFIRMED = 'confirmed';

    /** Admin rejected the payment. */
    case REJECTED = 'rejected';

    /** Payment refunded (e.g. request cancelled). */
    case REFUNDED = 'refunded';

    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }
}
