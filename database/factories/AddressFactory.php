<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $user = User::query()->first() ?? User::query()->create([
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('201#########'),
            'country_code' => '+20',
            'password' => 'password',
        ]);

        return [
            'addressable_type' => User::class,
            'addressable_id' => $user->id,
            'label' => fake()->randomElement(['home', 'office']),
            'type' => fake()->randomElement(['billing', 'shipping']),
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->secondaryAddress(),
            'street_name' => fake()->streetName(),
            'building' => (string) fake()->buildingNumber(),
            'floor' => (string) fake()->numberBetween(1, 20),
            'landmark' => fake()->optional()->sentence(3),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'is_primary' => false,
            'is_active' => true,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
