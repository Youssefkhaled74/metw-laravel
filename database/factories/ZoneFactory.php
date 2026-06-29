<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition(): array
    {
        return [
            'name_en' => 'Zone ' . fake()->unique()->word(),
            'name_ar' => 'منطقة ' . fake()->unique()->word(),
            'is_active' => true,
            'city_id' => City::factory(),
        ];
    }
}
