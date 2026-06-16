<?php

namespace Database\Seeders;

use App\Models\PackageType;
use Illuminate\Database\Seeder;

class PackageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Document', 'code' => 'DOC', 'description' => 'Paper documents and envelopes', 'is_active' => true],
            ['name' => 'Parcel', 'code' => 'PRC', 'description' => 'Small to medium parcels', 'is_active' => true],
            ['name' => 'Box', 'code' => 'BOX', 'description' => 'Standard box packaging', 'is_active' => true],
            ['name' => 'Pallet', 'code' => 'PAL', 'description' => 'Large pallet shipments', 'is_active' => true],
            ['name' => 'Fragile', 'code' => 'FRG', 'description' => 'Fragile items requiring special handling', 'is_active' => true],
            ['name' => 'Liquid', 'code' => 'LIQ', 'description' => 'Liquid items in sealed containers', 'is_active' => true],
            ['name' => 'Food', 'code' => 'FOOD', 'description' => 'Food and perishable items', 'is_active' => true],
        ];

        foreach ($types as $type) {
            PackageType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
