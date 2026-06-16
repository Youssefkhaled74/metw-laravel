<?php

namespace Database\Seeders;

use App\Models\Bannar;
use Illuminate\Database\Seeder;

class BannarSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'image'     => 'banners/home-hero-1.jpg',
                'link'      => 'https://lasco.test',
                'is_active' => true,
            ],
            [
                'image'     => 'banners/home-hero-2.jpg',
                'link'      => 'https://lasco.test/offers',
                'is_active' => true,
            ],
            [
                'image'     => 'banners/category-fashion.jpg',
                'link'      => 'https://lasco.test/categories/fashion',
                'is_active' => true,
            ],
        ];

        foreach ($banners as $data) {
            Bannar::updateOrCreate(
                ['image' => $data['image']],
                $data
            );
        }
    }
}


