<?php

namespace App\Enum;

enum RequestType: string
{
    case RETURN = 'return';
    case CANCELLATION = 'cancellation';
}
