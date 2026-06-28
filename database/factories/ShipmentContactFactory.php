<?php

namespace Database\Factories;

use App\Enum\ShipmentContactType;
use App\Models\ShipmentContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentContact>
 */
class ShipmentContactFactory extends Factory
{
    protected $model = ShipmentContact::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(ShipmentContactType::values()),
            'contact_number' => 'CNT-' . fake()->unique()->numerify('#####'),
            'full_name' => fake()->name(),
            'primary_mobile' => fake()->phoneNumber(),
            'secondary_mobile' => fake()->optional()->phoneNumber(),
        ];
    }
}
