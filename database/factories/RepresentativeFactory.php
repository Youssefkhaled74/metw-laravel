<?php

namespace Database\Factories;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Representative>
 */
class RepresentativeFactory extends Factory
{
    protected $model = Representative::class;

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
            'user_id' => $user->id,
            'warehouse_id' => null,
            'account_type' => fake()->randomElement([
                RepresentativeAccountType::FREE->value,
                RepresentativeAccountType::WAREHOUSE->value,
            ]),
            'status' => RepresentativeStatus::INCOMPLETE->value,
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'rejection_reason' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'suspended_at' => null,
            'is_active' => true,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
