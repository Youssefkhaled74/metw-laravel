<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSizeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_sizes')->insert([
            ['title' => 'Small',  'is_active' => true],
            ['title' => 'Medium', 'is_active' => true],
            ['title' => 'Large',  'is_active' => true],
            ['title' => 'XL',     'is_active' => true],
            ['title' => 'XXL',    'is_active' => true],
        ]);
    }
}
