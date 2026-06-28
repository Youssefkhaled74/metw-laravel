<?php

namespace Database\Factories;

use App\Models\AccountProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AccountProfile>
 */
class AccountProfileFactory extends Factory
{
    protected $model = AccountProfile::class;

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
            'profileable_type' => User::class,
            'profileable_id' => $user->id,
            'account_number' => 'ACC-' . Str::upper(Str::random(10)),
            'display_name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'alternate_phone' => fake()->optional()->phoneNumber(),
            'date_of_birth' => fake()->optional()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'national_id' => fake()->optional()->numerify('##############'),
            'preferred_locale' => fake()->randomElement(['en', 'ar']),
            'bio' => fake()->optional()->sentence(),
            'metadata' => ['source' => 'factory'],
        ];
    }
}
