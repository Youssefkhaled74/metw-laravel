<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name_en' => fake()->unique()->city(),
            'name_ar' => 'مدينة ' . fake()->unique()->word(),
            'is_active' => true,
            'state_id' => State::factory(),
            'governorate_id' => Governorate::factory(),
            'excel_sort' => fake()->numberBetween(1, 999),
            'is_capital' => false,
        ];
    }
}
