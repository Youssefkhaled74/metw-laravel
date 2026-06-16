<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case PICKUP = 'pickup';
    case ON_WAY = 'on_way';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';
}
