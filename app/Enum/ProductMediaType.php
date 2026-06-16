<?php

namespace App\Enum;

enum ProductMediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case EXTRA_IMAGE = 'extra_image';
    case COLOR_IMAGE = 'color_image';
}

