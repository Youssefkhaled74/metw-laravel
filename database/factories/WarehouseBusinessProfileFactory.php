<?php

namespace Database\Factories;

use App\Enum\BusinessProfileStatus;
use App\Models\Warehouse;
use App\Models\WarehouseBusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarehouseBusinessProfile>
 */
class WarehouseBusinessProfileFactory extends Factory
{
    protected $model = WarehouseBusinessProfile::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'legal_name' => fake()->company(),
            'commercial_name' => fake()->company(),
            'tax_number' => fake()->numerify('TAX-########'),
            'commercial_register_number' => fake()->numerify('CR-########'),
            'manager_name' => fake()->name(),
            'manager_phone' => fake()->phoneNumber(),
            'status' => BusinessProfileStatus::INCOMPLETE->value,
            'rejection_reason' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'metadata' => null,
        ];
    }
}
