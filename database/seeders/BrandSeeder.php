<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name_en'   => 'Nike',
                'name_ar'   => 'نايكي',
                'image'     => null,
                'is_active' => true,
            ],
            [
                'name_en'   => 'Adidas',
                'name_ar'   => 'أديداس',
                'image'     => null,
                'is_active' => true,
            ],
            [
                'name_en'   => 'Apple',
                'name_ar'   => 'آبل',
                'image'     => null,
                'is_active' => true,
            ],
            [
                'name_en'   => 'Samsung',
                'name_ar'   => 'سامسونج',
                'image'     => null,
                'is_active' => true,
            ],
        ];

        foreach ($brands as $data) {
            Brand::updateOrCreate(
                ['name_en' => $data['name_en']],
                $data
            );
        }
    }
}


