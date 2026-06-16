<?php

namespace App\Enum;

enum PaymentMethod: string
{
    case FULL = 'full';
    case PARTIAL = 'partial';
}
