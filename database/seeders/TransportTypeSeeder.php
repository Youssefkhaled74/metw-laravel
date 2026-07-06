<?php

namespace Database\Seeders;

use App\Models\TransportType;
use Illuminate\Database\Seeder;

class TransportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $transportTypes = [
            ['code' => '01', 'name_en' => 'Portable Bag with Public Transport', 'name_ar' => 'حقيبة محمولة مع النقل العام', 'max_weight' => 20, 'max_volume' => 0.05, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => false, 'requires_vehicle_license' => false],
            ['code' => '02', 'name_en' => 'Portable Box/Bag with Bicycle', 'name_ar' => 'صندوق أو حقيبة مع دراجة', 'max_weight' => 20, 'max_volume' => 0.05, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => false, 'requires_vehicle_license' => false],
            ['code' => '03', 'name_en' => 'Portable Box/Bag with Motorcycle', 'name_ar' => 'صندوق أو حقيبة مع دراجة نارية', 'max_weight' => 100, 'max_volume' => 0.22, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '04', 'name_en' => 'Portable Box/Bag with Vespa', 'name_ar' => 'صندوق أو حقيبة مع فيسبا', 'max_weight' => 100, 'max_volume' => 0.22, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '05', 'name_en' => 'Microbus', 'name_ar' => 'ميكروباص', 'max_weight' => 300, 'max_volume' => 0.80, 'category_1_available' => true, 'category_2_available' => true, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '06', 'name_en' => 'Travel Bus', 'name_ar' => 'أتوبيس سفر', 'max_weight' => 300, 'max_volume' => 0.80, 'category_1_available' => true, 'category_2_available' => true, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '07', 'name_en' => 'Private Car', 'name_ar' => 'سيارة خاصة', 'max_weight' => 700, 'max_volume' => 0.60, 'category_1_available' => true, 'category_2_available' => true, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '08', 'name_en' => 'Toktok', 'name_ar' => 'توك توك', 'max_weight' => 300, 'max_volume' => 0.80, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '09', 'name_en' => 'Tricycle', 'name_ar' => 'تروسیکل', 'max_weight' => 700, 'max_volume' => 4.00, 'category_1_available' => true, 'category_2_available' => false, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '10', 'name_en' => 'Small Truck', 'name_ar' => 'شاحنة صغيرة', 'max_weight' => 1200, 'max_volume' => 3.00, 'category_1_available' => false, 'category_2_available' => true, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '11', 'name_en' => 'Quarter Truck', 'name_ar' => 'ربع نقل', 'max_weight' => 1500, 'max_volume' => 7.00, 'category_1_available' => false, 'category_2_available' => true, 'category_3_available' => false, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '12', 'name_en' => '2 Ton Truck', 'name_ar' => 'سيارة نقل 2 طن', 'max_weight' => 2000, 'max_volume' => 8.00, 'category_1_available' => false, 'category_2_available' => true, 'category_3_available' => true, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '13', 'name_en' => '3 Ton Truck', 'name_ar' => 'سيارة نقل 3 طن', 'max_weight' => 3000, 'max_volume' => 12.00, 'category_1_available' => false, 'category_2_available' => true, 'category_3_available' => true, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '14', 'name_en' => '4 Ton Truck', 'name_ar' => 'سيارة نقل 4 طن', 'max_weight' => 4000, 'max_volume' => 18.00, 'category_1_available' => false, 'category_2_available' => false, 'category_3_available' => true, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '15', 'name_en' => '5 Ton Truck', 'name_ar' => 'سيارة نقل 5 طن', 'max_weight' => 5000, 'max_volume' => 26.00, 'category_1_available' => false, 'category_2_available' => false, 'category_3_available' => true, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
            ['code' => '16', 'name_en' => 'Heavy Equipment Transport', 'name_ar' => 'ناقلة معدات ثقيلة', 'max_weight' => null, 'max_volume' => null, 'category_1_available' => false, 'category_2_available' => false, 'category_3_available' => true, 'requires_driving_license' => true, 'requires_vehicle_license' => true],
        ];

        foreach ($transportTypes as $transportType) {
            TransportType::withTrashed()->updateOrCreate(
                ['code' => $transportType['code']],
                [
                    'name_en' => $transportType['name_en'],
                    'name_ar' => $transportType['name_ar'],
                    'description' => null,
                    'max_weight' => $transportType['max_weight'],
                    'max_volume' => $transportType['max_volume'],
                    'category_1_available' => $transportType['category_1_available'],
                    'category_2_available' => $transportType['category_2_available'],
                    'category_3_available' => $transportType['category_3_available'],
                    'requires_driving_license' => $transportType['requires_driving_license'],
                    'requires_vehicle_license' => $transportType['requires_vehicle_license'],
                    'is_active' => true,
                    'metadata' => [
                        'unlimited_capacity' => $transportType['max_weight'] === null && $transportType['max_volume'] === null,
                    ],
                    'deleted_at' => null,
                ]
            );
        }
    }
}
