<?php

namespace Database\Factories;

use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GovernorateFactory extends Factory
{
    protected $model = Governorate::class;

    public function definition(): array
    {
        return [
            'governorate_number' => fake()->unique()->numberBetween(1, 999),
            'name_ar' => 'محافظة ' . fake()->unique()->word(),
            'capital_city_id' => null,
            'is_active' => true,
        ];
    }
}
