<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name_en' => fake()->unique()->country(),
            'name_ar' => 'دولة ' . fake()->unique()->word(),
            'is_active' => true,
            'phone_code' => '+20',
        ];
    }
}
