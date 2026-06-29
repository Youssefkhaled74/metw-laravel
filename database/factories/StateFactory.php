<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition(): array
    {
        return [
            'name_en' => fake()->unique()->state(),
            'name_ar' => 'محافظة ' . fake()->unique()->word(),
            'is_active' => true,
            'country_id' => Country::factory(),
        ];
    }
}
