<?php

namespace App\Enum;

enum AddressType: string
{
    case PICKUP = 'pickup';
    case DROPOFF = 'dropoff';
}
