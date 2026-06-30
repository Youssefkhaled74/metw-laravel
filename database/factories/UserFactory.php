<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'father_name' => fake()->optional()->firstName(),
            'last_name' => fake()->optional()->lastName(),
            'birth_date' => fake()->optional()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'national_id' => fake()->boolean(70) ? fake()->unique()->numerify('##############') : null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('01#########'),
            'country_code' => '+20',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'notifications_enabled' => true,
            'remember_token' => Str::random(10),
            'mobile_primary' => fake()->boolean(50) ? fake()->unique()->numerify('015########') : null,
            'mobile_secondary' => null,
            'mobile_primary_verified_at' => now(),
            'mobile_secondary_verified_at' => null,
            'enable_shipment_notifications' => true,
            'default_shipment_lang' => 'ar',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
