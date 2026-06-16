<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title'     => 'About Lasco',
                'slug'      => Str::slug('About Lasco'),
                'type'      => 'static',
                'content'   => 'Lasco is a shipment and ecommerce platform providing fast and reliable delivery.',
                'is_active' => true,
            ],
            [
                'title'     => 'Terms and Conditions',
                'slug'      => Str::slug('Terms and Conditions'),
                'type'      => 'static',
                'content'   => 'These are the general terms and conditions for using Lasco services.',
                'is_active' => true,
            ],
            [
                'title'     => 'Privacy Policy',
                'slug'      => Str::slug('Privacy Policy'),
                'type'      => 'static',
                'content'   => 'We respect your privacy and handle your data with care.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}


