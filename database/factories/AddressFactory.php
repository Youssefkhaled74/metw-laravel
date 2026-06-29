<?php

namespace Database\Factories;

use App\Enum\AddressType;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\State;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $country = Country::factory();
        $state = State::factory()->state(fn () => ['country_id' => $country]);
        $governorate = Governorate::factory();
        $city = City::factory()->state(fn () => [
            'state_id' => $state,
            'governorate_id' => $governorate,
        ]);
        $zone = Zone::factory()->state(fn () => ['city_id' => $city]);

        return [
            'addressable_type' => User::class,
            'addressable_id' => User::factory(),
            'label' => fake()->randomElement(['home', 'office']),
            'type' => fake()->randomElement(AddressType::values()),
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'country_id' => $country,
            'state_id' => $state,
            'governorate_id' => $governorate,
            'city_id' => $city,
            'zone_id' => $zone,
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
