<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'brand_name' => fake()->companySuffix(),
            'email' => fake()->unique()->safeEmail(),
            'country_code' => '+20',
            'phone' => fake()->unique()->numerify('01#########'),
            'password' => Hash::make('password'),
            'address' => fake()->address(),
            'latitude' => (string) fake()->latitude(),
            'longitude' => (string) fake()->longitude(),
            'logo' => null,
            'email_verified' => true,
            'phone_verified' => true,
            'is_active' => true,
            'fcm_token' => null,
            'remember_token' => null,
        ];
    }
}
