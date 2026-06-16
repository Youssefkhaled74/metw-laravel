<?php

namespace App\Enum;

enum LocationType: string
{
    case COUNTRY = 'country';
    case STATE = 'state';
    case CITY = 'city';
}
