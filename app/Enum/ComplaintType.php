<?php

namespace App\Enum;

enum ComplaintType: string
{
    case PURCHASE_CANCELLATION = 'purchase_cancellation';
    case SHIPPING_CANCELLATION = 'shipping_cancellation';
    case RETURN = 'return';
    case USER = 'user';
    case VENDOR = 'vendor';
    case WAREHOUSE = 'warehouse';
    case REPRESENTATIVE = 'representative';
}
