<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductColorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_colors')->insert([
            ['name' => 'Red',    'hex' => '#FF0000', 'is_active' => true],
            ['name' => 'Green',  'hex' => '#00FF00', 'is_active' => true],
            ['name' => 'Blue',   'hex' => '#0000FF', 'is_active' => true],
            ['name' => 'Black',  'hex' => '#000000', 'is_active' => true],
            ['name' => 'White',  'hex' => '#FFFFFF', 'is_active' => true],
        ]);
    }
}
