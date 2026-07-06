<?php

namespace App\Enum;

enum RepresentativeDocumentType: string
{
    case PERSONAL_PHOTO = 'personal_photo';
    case NATIONAL_ID_FRONT = 'national_id_front';
    case NATIONAL_ID_BACK = 'national_id_back';
    case VEHICLE_PHOTO = 'vehicle_photo';
    case DRIVING_LICENSE_FRONT = 'driving_license_front';
    case DRIVING_LICENSE_BACK = 'driving_license_back';
    case VEHICLE_LICENSE_FRONT = 'vehicle_license_front';
    case VEHICLE_LICENSE_BACK = 'vehicle_license_back';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}
