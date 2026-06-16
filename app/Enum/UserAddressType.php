<?php

namespace App\Enum;

enum UserAddressType: string
{
    case HOME = 'home';
    case OFFICE = 'office';
    case OTHER = 'other';
}

