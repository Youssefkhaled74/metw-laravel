<?php

namespace Database\Seeders;

use App\Models\RepresentativeWorkTypeOption;
use Illuminate\Database\Seeder;

class RepresentativeWorkTypeOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            [
                'code' => 'local_delivery',
                'name_en' => 'Local Delivery',
                'name_ar' => 'توصيل محلي',
                'description' => 'Delivery inside the same governorate or city network.',
                'sort_order' => 1,
                'is_exclusive' => false,
            ],
            [
                'code' => 'inter_governorate_shipping',
                'name_en' => 'Inter Governorate Shipping',
                'name_ar' => 'شحن بين المحافظات',
                'description' => 'Delivery between different governorates.',
                'sort_order' => 2,
                'is_exclusive' => false,
            ],
            [
                'code' => 'bus_driver',
                'name_en' => 'Bus Driver',
                'name_ar' => 'سائق أتوبيس بين المحافظات',
                'description' => 'Transport handled by a bus driver between governorates.',
                'sort_order' => 3,
                'is_exclusive' => true,
            ],
        ];

        foreach ($options as $option) {
            RepresentativeWorkTypeOption::withTrashed()->updateOrCreate(
                ['code' => $option['code']],
                $option + [
                    'is_active' => true,
                    'metadata' => null,
                    'deleted_at' => null,
                ]
            );
        }
    }
}
