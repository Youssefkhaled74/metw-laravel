<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::insert([
            ['name' => 'Cairo', 'is_active' => true],
            ['name' => 'Alexandria', 'is_active' => true],
            ['name' => 'Giza', 'is_active' => true],
            ['name' => 'Suez', 'is_active' => true],
            ['name' => 'Port Said', 'is_active' => true],
        ]);
    }
}
