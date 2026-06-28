<?php

namespace Database\Factories;

use App\Models\TransportType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TransportType>
 */
class TransportTypeFactory extends Factory
{
    protected $model = TransportType::class;

    public function definition(): array
    {
        return [
            'code' => 'TRN-' . Str::upper(Str::random(6)),
            'name_en' => fake()->randomElement(['Motorbike', 'Car', 'Van', 'Truck']),
            'name_ar' => fake()->randomElement(['دراجة نارية', 'سيارة', 'فان', 'شاحنة']),
            'description' => fake()->sentence(),
            'max_weight' => fake()->randomFloat(2, 5, 5000),
            'max_volume' => fake()->randomFloat(2, 1, 500),
            'is_active' => true,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
