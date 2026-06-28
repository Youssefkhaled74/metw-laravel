<?php

namespace Database\Seeders;

use App\Models\TransportType;
use Illuminate\Database\Seeder;

class TransportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $transportTypes = [
            [
                'code' => '01',
                'name_en' => 'Carried Bag with Public Transportation',
                'name_ar' => 'حقيبة محمولة بوسائل النقل العامة',
                'description' => 'Public transportation hand-carried bag',
                'max_weight' => 20,
                'max_volume' => 0.05,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '02',
                'name_en' => 'Box/Bag with Bicycle',
                'name_ar' => 'صندوق أو حقيبة بدراجة',
                'description' => 'Bicycle delivery for light parcels',
                'max_weight' => 20,
                'max_volume' => 0.05,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '03',
                'name_en' => 'Box/Bag with Motorcycle',
                'name_ar' => 'صندوق أو حقيبة بدراجة نارية',
                'description' => 'Motorcycle delivery for small parcels',
                'max_weight' => 100,
                'max_volume' => 0.22,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '04',
                'name_en' => 'Box/Bag with Vespa',
                'name_ar' => 'صندوق أو حقيبة بفيسبا',
                'description' => 'Vespa delivery for small parcels',
                'max_weight' => 100,
                'max_volume' => 0.22,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '05',
                'name_en' => 'Microbus',
                'name_ar' => 'ميكروباص',
                'description' => 'Microbus transport',
                'max_weight' => 300,
                'max_volume' => 0.80,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '06',
                'name_en' => 'Travel Bus',
                'name_ar' => 'أتوبيس سفر',
                'description' => 'Travel bus transport',
                'max_weight' => 300,
                'max_volume' => 0.80,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '07',
                'name_en' => 'Private Car',
                'name_ar' => 'سيارة خاصة',
                'description' => 'Private car transport',
                'max_weight' => 700,
                'max_volume' => 0.60,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '08',
                'name_en' => 'Tuk-Tuk',
                'name_ar' => 'توك توك',
                'description' => 'Tuk-tuk transport',
                'max_weight' => 300,
                'max_volume' => 0.80,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '09',
                'name_en' => 'Tricycle',
                'name_ar' => 'تروسيكل',
                'description' => 'Tricycle cargo transport',
                'max_weight' => 700,
                'max_volume' => 4.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '10',
                'name_en' => 'Small Pickup',
                'name_ar' => 'بيك أب صغير',
                'description' => 'Small pickup transport',
                'max_weight' => 1200,
                'max_volume' => 3.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '11',
                'name_en' => 'Quarter Truck',
                'name_ar' => 'ربع نقل',
                'description' => 'Quarter truck transport',
                'max_weight' => 1500,
                'max_volume' => 7.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '12',
                'name_en' => '2-Ton Truck',
                'name_ar' => 'سيارة نقل 2 طن',
                'description' => '2-ton truck transport',
                'max_weight' => 2000,
                'max_volume' => 8.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '13',
                'name_en' => '3-Ton Truck',
                'name_ar' => 'سيارة نقل 3 طن',
                'description' => '3-ton truck transport',
                'max_weight' => 3000,
                'max_volume' => 12.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '14',
                'name_en' => '4-Ton Truck',
                'name_ar' => 'سيارة نقل 4 طن',
                'description' => '4-ton truck transport',
                'max_weight' => 4000,
                'max_volume' => 18.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '15',
                'name_en' => '5-Ton Truck',
                'name_ar' => 'سيارة نقل 5 طن',
                'description' => '5-ton truck transport',
                'max_weight' => 5000,
                'max_volume' => 26.00,
                'metadata' => ['unlimited_capacity' => false],
            ],
            [
                'code' => '16',
                'name_en' => 'Heavy Equipment Carrier',
                'name_ar' => 'ناقلة معدات ثقيلة',
                'description' => 'Heavy equipment carrier with unlimited weight and volume',
                'max_weight' => null,
                'max_volume' => null,
                'metadata' => ['unlimited_capacity' => true],
            ],
        ];

        foreach ($transportTypes as $transportType) {
            TransportType::withTrashed()->updateOrCreate(
                ['code' => $transportType['code']],
                [
                    'name_en' => $transportType['name_en'],
                    'name_ar' => $transportType['name_ar'],
                    'description' => $transportType['description'],
                    'max_weight' => $transportType['max_weight'],
                    'max_volume' => $transportType['max_volume'],
                    'is_active' => true,
                    'metadata' => $transportType['metadata'],
                    'deleted_at' => null,
                ]
            );
        }
    }
}
