<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Warehouse;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Warehouse',
            'phone' => fake()->phoneNumber(),
            'country_id' => Country::query()->value('id'),
            'state_id' => State::query()->value('id'),
            'city_id' => City::withoutGlobalScopes()->value('id'),
            'zone_id' => Zone::query()->value('id'),
            'street_name' => fake()->streetName(),
            'building' => fake()->buildingNumber(),
            'floor' => (string) fake()->numberBetween(1, 10),
            'landmark' => fake()->optional()->streetAddress(),
            'address_type' => 'warehouse',
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'is_main' => false,
        ];
    }
}
