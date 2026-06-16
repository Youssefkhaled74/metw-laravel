<?php

namespace Database\Seeders;

use App\Models\CancelReason;
use Illuminate\Database\Seeder;

class CancelReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name_en' => 'Item arrived damaged', 'name_ar' => 'المنتج وصل تالف', 'is_active' => true],
            ['name_en' => 'Wrong item received', 'name_ar' => 'تم استلام منتج خطأ', 'is_active' => true],
            ['name_en' => 'Quality not satisfactory', 'name_ar' => 'الجودة غير مرضية', 'is_active' => true],
            ['name_en' => 'Item not as described', 'name_ar' => 'المنتج ليس كما هو موصوف', 'is_active' => true],
            ['name_en' => 'Changed my mind', 'name_ar' => 'غيرت رأيي', 'is_active' => true],
            ['name_en' => 'Found better price elsewhere', 'name_ar' => 'وجدت سعر أفضل في مكان آخر', 'is_active' => true],
            ['name_en' => 'Delivery too late', 'name_ar' => 'التوصيل متأخر جدا', 'is_active' => true],
            ['name_en' => 'Ordered by mistake', 'name_ar' => 'تم الطلب بالخطأ', 'is_active' => true],
            ['name_en' => 'Duplicate order', 'name_ar' => 'طلب مكرر', 'is_active' => true],
            ['name_en' => 'Item out of stock', 'name_ar' => 'المنتج غير متوفر', 'is_active' => true],
        ];

        foreach ($reasons as $reason) {
            CancelReason::updateOrCreate(
                ['name_en' => $reason['name_en']],
                $reason
            );
        }
    }
}
