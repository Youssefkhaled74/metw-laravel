<?php

namespace App\Enum;

enum CartStatus: string
{
    case OPEN = 'open';
    case CHECKED_OUT = 'checked_out';
    case ABANDONED = 'abandoned';
}
