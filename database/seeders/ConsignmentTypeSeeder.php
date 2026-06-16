<?php

namespace Database\Seeders;

use App\Models\ConsignmentType;
use Illuminate\Database\Seeder;

class ConsignmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Standard Parcel',
                'code'        => 'STD_PARCEL',
                'description' => 'Regular parcels up to 20kg.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Fragile Goods',
                'code'        => 'FRAGILE',
                'description' => 'Special handling for fragile items.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Documents',
                'code'        => 'DOCS',
                'description' => 'Important documents and paperwork.',
                'is_active'   => true,
            ],
        ];

        foreach ($types as $data) {
            ConsignmentType::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}


