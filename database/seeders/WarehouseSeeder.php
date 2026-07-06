<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\State;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $country = $this->findCountry(['Egypt', 'مصر']);

        if (! $country) {
            $country = Country::withoutGlobalScopes()->create([
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'phone_code' => '+20',
                'is_active' => true,
            ]);
        }

        $warehouses = [
            [
                'name' => 'Cairo Main Warehouse',
                'phone' => '+201000000001',
                'governorate' => ['القاهرة', 'Cairo'],
                'city' => ['القاهرة', 'Cairo'],
                'street_name' => 'Nasr City',
                'building' => '12',
                'floor' => '1',
                'landmark' => 'Near City Stars',
                'latitude' => 30.0725,
                'longitude' => 31.2841,
                'is_main' => true,
            ],
            [
                'name' => 'Alexandria North Warehouse',
                'phone' => '+201000000002',
                'governorate' => ['الإسكندرية', 'Alexandria'],
                'city' => ['الإسكندرية', 'Alexandria'],
                'street_name' => 'Smouha',
                'building' => '8',
                'floor' => '2',
                'landmark' => 'Near Stanley Bridge',
                'latitude' => 31.2019,
                'longitude' => 29.9187,
                'is_main' => false,
            ],
            [
                'name' => 'Giza Fulfillment Warehouse',
                'phone' => '+201000000003',
                'governorate' => ['الجيزة', 'Giza'],
                'city' => ['الجيزة', 'Giza'],
                'street_name' => '6th of October',
                'building' => '24',
                'floor' => 'G',
                'landmark' => 'Near Mall of Egypt',
                'latitude' => 29.9737,
                'longitude' => 31.1300,
                'is_main' => false,
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            $governorate = $this->findGovernorate($warehouseData['governorate']);

            if (! $governorate) {
                continue;
            }

            $state = $this->findStateByGovernorate($governorate);
            $city = $this->findCity($warehouseData['city'], $governorate->id, $state?->id);

            if (! $city) {
                continue;
            }

            $warehouse = Warehouse::withTrashed()->updateOrCreate(
                ['name' => $warehouseData['name']],
                [
                    'phone' => $warehouseData['phone'],
                    'country_id' => $country->id,
                    'governorate_id' => $governorate->id,
                    'state_id' => $state?->id,
                    'city_id' => $city->id,
                    'street_name' => $warehouseData['street_name'],
                    'building' => $warehouseData['building'],
                    'floor' => $warehouseData['floor'],
                    'landmark' => $warehouseData['landmark'],
                    'address_type' => 'warehouse',
                    'latitude' => $warehouseData['latitude'],
                    'longitude' => $warehouseData['longitude'],
                    'is_main' => $warehouseData['is_main'],
                ]
            );

            if ($warehouse->trashed()) {
                $warehouse->restore();
            }
        }

        $mainWarehouse = Warehouse::withoutGlobalScopes()->where('is_main', true)->first();

        if ($mainWarehouse) {
            Warehouse::withoutGlobalScopes()
                ->where('id', '!=', $mainWarehouse->id)
                ->update(['is_main' => false]);
        }
    }

    private function findCountry(array $names): ?Country
    {
        return Country::withoutGlobalScopes()
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_en', $name)
                        ->orWhere('name_ar', $name);
                }
            })
            ->first();
    }

    private function findGovernorate(array $names): ?Governorate
    {
        return Governorate::withoutGlobalScopes()
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_ar', $name);
                }
            })
            ->first();
    }

    private function findStateByGovernorate(Governorate $governorate): ?State
    {
        return State::withoutGlobalScopes()
            ->where(function ($query) use ($governorate) {
                $query->where('name_ar', $governorate->name_ar)
                    ->orWhere('name_en', $governorate->name_ar);
            })
            ->first();
    }

    private function findCity(array $names, int $governorateId, ?int $stateId): ?City
    {
        $city = City::withoutGlobalScopes()
            ->where('governorate_id', $governorateId)
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_ar', $name)
                        ->orWhere('name_en', $name);
                }
            })
            ->first();

        if ($city) {
            return $city;
        }

        if (! $stateId) {
            return null;
        }

        return City::withoutGlobalScopes()
            ->where('state_id', $stateId)
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_ar', $name)
                        ->orWhere('name_en', $name);
                }
            })
            ->first();
    }
}
