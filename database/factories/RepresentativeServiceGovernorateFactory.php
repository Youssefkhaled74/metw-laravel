<?php

namespace Database\Factories;

use App\Models\Governorate;
use App\Models\Representative;
use App\Models\RepresentativeServiceGovernorate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepresentativeServiceGovernorate>
 */
class RepresentativeServiceGovernorateFactory extends Factory
{
    protected $model = RepresentativeServiceGovernorate::class;

    public function definition(): array
    {
        $governorate = Governorate::query()->first() ?? Governorate::query()->create([
            'governorate_number' => fake()->unique()->numberBetween(100, 999),
            'name_ar' => 'محافظة ' . fake()->unique()->word(),
            'is_active' => true,
        ]);

        return [
            'representative_id' => Representative::factory(),
            'governorate_id' => $governorate->id,
        ];
    }
}
