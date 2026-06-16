<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
}
