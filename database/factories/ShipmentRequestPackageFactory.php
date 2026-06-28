<?php

namespace Database\Factories;

use App\Models\ShipmentRequest;
use App\Models\ShipmentRequestPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentRequestPackage>
 */
class ShipmentRequestPackageFactory extends Factory
{
    protected $model = ShipmentRequestPackage::class;

    public function definition(): array
    {
        return [
            'shipment_request_id' => ShipmentRequest::factory(),
            'package_name' => fake()->words(3, true),
            'package_type' => fake()->randomElement(['box', 'document', 'bag', 'fragile']),
            'quantity' => fake()->numberBetween(1, 5),
            'weight' => fake()->randomFloat(2, 0.5, 50),
            'length' => fake()->randomFloat(2, 5, 100),
            'width' => fake()->randomFloat(2, 5, 100),
            'height' => fake()->randomFloat(2, 2, 100),
            'declared_value' => fake()->randomFloat(2, 100, 5000),
            'notes' => fake()->optional()->sentence(),
            'metadata' => null,
        ];
    }
}
