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
        $country = Country::factory();
        $state = State::factory()->state(fn () => ['country_id' => $country]);
        $city = City::factory()->state(fn () => ['state_id' => $state]);
        $zone = Zone::factory()->state(fn () => ['city_id' => $city]);

        return [
            'name' => fake()->company() . ' Warehouse',
            'phone' => fake()->phoneNumber(),
            'country_id' => $country,
            'state_id' => $state,
            'city_id' => $city,
            'zone_id' => $zone,
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
