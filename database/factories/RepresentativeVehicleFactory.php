<?php

namespace Database\Factories;

use App\Models\Representative;
use App\Models\RepresentativeVehicle;
use App\Models\TransportType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepresentativeVehicle>
 */
class RepresentativeVehicleFactory extends Factory
{
    protected $model = RepresentativeVehicle::class;

    public function definition(): array
    {
        $transportType = TransportType::query()->first() ?? TransportType::factory()->create();

        return [
            'representative_id' => Representative::factory(),
            'transport_type_id' => $transportType->id,
            'registration_number' => fake()->unique()->bothify('REP-####'),
            'license_number' => fake()->bothify('LIC-#####'),
            'brand' => fake()->company(),
            'model' => fake()->word(),
            'color' => fake()->safeColorName(),
            'manufacture_year' => fake()->numberBetween(2000, (int) date('Y') + 1),
            'max_weight' => fake()->randomFloat(2, 20, 5000),
            'max_volume' => fake()->randomFloat(2, 0.05, 30),
            'is_active' => true,
            'notes' => fake()->optional()->sentence(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
