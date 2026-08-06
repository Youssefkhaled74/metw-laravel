<?php

namespace Database\Seeders;

use App\Enum\BusinessProfileStatus;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\State;
use App\Models\Warehouse;
use App\Models\WarehouseBusinessProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $country = $this->firstOrCreateCountry();

        $warehouses = [
            [
                'name' => 'Cairo Main Warehouse',
                'phone' => '+201000000001',
                'governorate' => ['Cairo', 'القاهرة'],
                'city' => ['Cairo', 'القاهرة'],
                'district_or_village_name' => 'Nasr City',
                'district_or_village_type' => 'district',
                'street_name' => 'Nasr City',
                'branch_from_street' => 'Mokattam Branch Road',
                'building_number' => 12,
                'floor_number' => 1,
                'building_name' => 'City Stars Tower',
                'nearby_landmark' => 'City Stars',
                'address_description' => 'Main warehouse used for dashboard testing.',
                'building' => '12',
                'floor' => '1',
                'landmark' => 'Near City Stars',
                'latitude' => 30.0725,
                'longitude' => 31.2841,
                'is_main' => true,
                'business_profile' => [
                    'status' => BusinessProfileStatus::APPROVED->value,
                    'legal_name' => 'Cairo Main Warehouse LLC',
                    'commercial_name' => 'Cairo Main Warehouse',
                    'tax_number' => 'TAX-CAIRO-0001',
                    'commercial_register_number' => 'CR-CAIRO-0001',
                    'manager_name' => 'Ahmed Hassan',
                    'manager_phone' => '+201000000101',
                ],
            ],
            [
                'name' => 'Alexandria North Warehouse',
                'phone' => '+201000000002',
                'governorate' => ['Alexandria', 'الإسكندرية'],
                'city' => ['Alexandria', 'الإسكندرية'],
                'district_or_village_name' => 'Smouha',
                'district_or_village_type' => 'district',
                'street_name' => 'Smouha',
                'branch_from_street' => 'Al Horreya Road',
                'building_number' => 8,
                'floor_number' => 2,
                'building_name' => 'North Hub',
                'nearby_landmark' => 'Stanley Bridge',
                'address_description' => 'Secondary warehouse for north region flow.',
                'building' => '8',
                'floor' => '2',
                'landmark' => 'Near Stanley Bridge',
                'latitude' => 31.2019,
                'longitude' => 29.9187,
                'is_main' => false,
                'business_profile' => [
                    'status' => BusinessProfileStatus::PENDING_REVIEW->value,
                    'legal_name' => 'Alexandria North Logistics',
                    'commercial_name' => 'Alexandria North Warehouse',
                    'tax_number' => 'TAX-ALEX-0002',
                    'commercial_register_number' => 'CR-ALEX-0002',
                    'manager_name' => 'Mona Salem',
                    'manager_phone' => '+201000000102',
                ],
            ],
            [
                'name' => 'Giza Fulfillment Warehouse',
                'phone' => '+201000000003',
                'governorate' => ['Giza', 'الجيزة'],
                'city' => ['Giza', 'الجيزة'],
                'district_or_village_name' => '6th of October',
                'district_or_village_type' => 'district',
                'street_name' => '6th of October',
                'branch_from_street' => 'Al Mehwar Road',
                'building_number' => 24,
                'floor_number' => 0,
                'building_name' => 'Giza Gate',
                'nearby_landmark' => 'Mall of Egypt',
                'address_description' => 'Fulfillment warehouse for heavy shipment scenarios.',
                'building' => '24',
                'floor' => 'G',
                'landmark' => 'Near Mall of Egypt',
                'latitude' => 29.9737,
                'longitude' => 31.1300,
                'is_main' => false,
                'business_profile' => [
                    'status' => BusinessProfileStatus::REJECTED->value,
                    'rejection_reason' => 'Incomplete commercial registration documents.',
                    'legal_name' => 'Giza Fulfillment Trading',
                    'commercial_name' => 'Giza Fulfillment Warehouse',
                    'tax_number' => 'TAX-GIZA-0003',
                    'commercial_register_number' => 'CR-GIZA-0003',
                    'manager_name' => 'Karim Nader',
                    'manager_phone' => '+201000000103',
                ],
            ],
        ];

        foreach ($warehouses as $warehouseData) {
            $governorate = $this->firstOrCreateGovernorate($warehouseData['governorate']);
            $state = $this->firstOrCreateState($country->id, $warehouseData['governorate']);
            $city = $this->firstOrCreateCity($warehouseData['city'], $governorate->id, $state->id);

            $warehouse = Warehouse::withTrashed()->updateOrCreate(
                ['name' => $warehouseData['name']],
                $this->filterExistingColumns('warehouses', [
                    'phone' => $warehouseData['phone'],
                    'country_id' => $country->id,
                    'governorate_id' => $governorate->id,
                    'state_id' => $state->id,
                    'city_id' => $city->id,
                    'district_or_village_name' => $warehouseData['district_or_village_name'],
                    'district_or_village_type' => $warehouseData['district_or_village_type'],
                    'street_name' => $warehouseData['street_name'],
                    'branch_from_street' => $warehouseData['branch_from_street'],
                    'building_number' => $warehouseData['building_number'],
                    'building' => $warehouseData['building'],
                    'floor_number' => $warehouseData['floor_number'],
                    'floor' => $warehouseData['floor'],
                    'building_name' => $warehouseData['building_name'],
                    'nearby_landmark' => $warehouseData['nearby_landmark'],
                    'landmark' => $warehouseData['landmark'],
                    'address_description' => $warehouseData['address_description'],
                    'address_type' => 'warehouse',
                    'latitude' => $warehouseData['latitude'],
                    'longitude' => $warehouseData['longitude'],
                    'is_main' => $warehouseData['is_main'],
                ])
            );

            if ($warehouse->trashed()) {
                $warehouse->restore();
            }

            WarehouseBusinessProfile::withTrashed()->updateOrCreate(
                ['warehouse_id' => $warehouse->id],
                [
                    'legal_name' => $warehouseData['business_profile']['legal_name'],
                    'commercial_name' => $warehouseData['business_profile']['commercial_name'],
                    'tax_number' => $warehouseData['business_profile']['tax_number'],
                    'commercial_register_number' => $warehouseData['business_profile']['commercial_register_number'],
                    'manager_name' => $warehouseData['business_profile']['manager_name'],
                    'manager_phone' => $warehouseData['business_profile']['manager_phone'],
                    'status' => $warehouseData['business_profile']['status'],
                    'rejection_reason' => $warehouseData['business_profile']['rejection_reason'] ?? null,
                    'submitted_at' => now()->subDays(7),
                    'reviewed_at' => in_array($warehouseData['business_profile']['status'], [
                        BusinessProfileStatus::APPROVED->value,
                        BusinessProfileStatus::REJECTED->value,
                    ], true) ? now()->subDays(3) : null,
                    'approved_at' => $warehouseData['business_profile']['status'] === BusinessProfileStatus::APPROVED->value ? now()->subDays(1) : null,
                    'metadata' => ['seeded' => true],
                    'deleted_at' => null,
                ]
            );
        }

        $mainWarehouse = Warehouse::withoutGlobalScopes()->where('is_main', true)->first();

        if ($mainWarehouse) {
            Warehouse::withoutGlobalScopes()
                ->where('id', '!=', $mainWarehouse->id)
                ->update(['is_main' => false]);
        }
    }

    private function firstOrCreateCountry(): Country
    {
        $country = Country::withoutGlobalScopes()
            ->where(function ($query) {
                $query->where('name_en', 'Egypt')
                    ->orWhere('name_ar', 'مصر');
            })
            ->first();

        if ($country) {
            return $country;
        }

        return Country::withoutGlobalScopes()->create([
            'name_en' => 'Egypt',
            'name_ar' => 'مصر',
            'phone_code' => '+20',
            'is_active' => true,
        ]);
    }

    private function firstOrCreateGovernorate(array $names): Governorate
    {
        $governorate = Governorate::withoutGlobalScopes()
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_ar', $name);
                }
            })
            ->first();

        if ($governorate) {
            return $governorate;
        }

        return Governorate::withoutGlobalScopes()->create([
            'governorate_number' => ((int) (Governorate::withoutGlobalScopes()->max('governorate_number') ?? 0)) + 1,
            'name_ar' => $names[1] ?? $names[0],
            'is_active' => true,
        ]);
    }

    private function firstOrCreateState(int $countryId, array $names): State
    {
        $state = State::withoutGlobalScopes()
            ->where(function ($query) use ($names) {
                foreach ($names as $name) {
                    $query->orWhere('name_ar', $name)
                        ->orWhere('name_en', $name);
                }
            })
            ->first();

        if ($state) {
            return $state;
        }

        return State::withoutGlobalScopes()->create([
            'name_en' => $names[0],
            'name_ar' => $names[1] ?? $names[0],
            'country_id' => $countryId,
            'is_active' => true,
        ]);
    }

    private function firstOrCreateCity(array $names, int $governorateId, int $stateId): City
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

        return City::withoutGlobalScopes()->create([
            'name_en' => $names[0],
            'name_ar' => $names[1] ?? $names[0],
            'state_id' => $stateId,
            'governorate_id' => $governorateId,
            'excel_sort' => ((int) (City::withoutGlobalScopes()->where('governorate_id', $governorateId)->max('excel_sort') ?? 0)) + 1,
            'is_capital' => false,
            'is_active' => true,
        ]);
    }

    private function filterExistingColumns(string $table, array $attributes): array
    {
        return collect($attributes)
            ->filter(fn ($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }
}
