<?php

namespace App\Enum;

enum ReturnStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case PICKUP = 'pickup';
    case PROCESSING = 'processing';
    case REFUNDED = 'refunded';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
