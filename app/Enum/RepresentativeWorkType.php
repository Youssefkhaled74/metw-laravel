<?php

namespace App\Enum;

enum RepresentativeWorkType: string
{
    case LOCAL_DELIVERY = 'local_delivery';
    case INTER_GOVERNORATE_SHIPPING = 'inter_governorate_shipping';
    case BUS_DRIVER = 'bus_driver';
}
