<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['title' => 'Small Box',  'icon' => null, 'is_active' => true],
            ['title' => 'Medium Box', 'icon' => null, 'is_active' => true],
            ['title' => 'Large Box',  'icon' => null, 'is_active' => true],
        ];

        foreach ($sizes as $data) {
            Size::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}


