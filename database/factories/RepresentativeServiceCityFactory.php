<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Representative;
use App\Models\RepresentativeServiceCity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepresentativeServiceCity>
 */
class RepresentativeServiceCityFactory extends Factory
{
    protected $model = RepresentativeServiceCity::class;

    public function definition(): array
    {
        $governorate = Governorate::query()->first() ?? Governorate::query()->create([
            'governorate_number' => fake()->unique()->numberBetween(100, 999),
            'name_ar' => 'محافظة ' . fake()->unique()->word(),
            'is_active' => true,
        ]);

        $city = City::query()->first() ?? City::query()->create([
            'name_en' => fake()->city(),
            'name_ar' => 'مدينة ' . fake()->unique()->word(),
            'is_active' => true,
            'governorate_id' => $governorate->id,
        ]);

        return [
            'representative_id' => Representative::factory(),
            'city_id' => $city->id,
        ];
    }
}
