<?php

namespace App\Enum;

enum ReturnReason: string
{
    case ITEM_ARRIVED_DAMAGED = 'item_arrived_damaged';
    case WRONG_ITEM = 'wrong_item';
    case QUALITY_NOT_SATISFACTORY = 'quality_not_satisfactory';
    case ITEM_NOT_AS_DESCRIBED = 'item_not_as_described';
    case OTHER = 'other';
}
