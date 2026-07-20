<?php

namespace App\Enum;

enum CancellationSellerStatus: string
{
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case UNDER_INSPECTION = 'under_inspection';
    case RETURN_ACCEPTED = 'return_accepted';
    case RETURN_REJECTED = 'return_rejected';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'طلب إلغاء موافق عليه',
            self::REJECTED => 'شكوى طلب إلغاء مرفوض',
            self::UNDER_INSPECTION => 'تم استلام المنتج وجاري الفحص',
            self::RETURN_ACCEPTED => 'مرتجع مقبول',
            self::RETURN_REJECTED => 'طلب إلغاء شكوى مرتجع مرفوض',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::UNDER_INSPECTION => 'warning',
            self::RETURN_ACCEPTED => 'success',
            self::RETURN_REJECTED => 'danger',
        };
    }
}
