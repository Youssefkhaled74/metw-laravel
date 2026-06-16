<?php

namespace Database\Seeders;

use App\Models\ShipmentCompany;
use App\Models\Category;
use App\Models\ShipmentCompanyCategoryPrice;
use Illuminate\Database\Seeder;

class ShipmentCompanyCategoryPriceSeeder extends Seeder
{
    public function run(): void
    {
        $companies = ShipmentCompany::all();
        $categories = Category::inRandomOrder()->take(10)->get();

        foreach ($companies as $company) {
            foreach ($categories as $category) {
                ShipmentCompanyCategoryPrice::updateOrCreate(
                    [
                        'shipment_company_id' => $company->id,
                        'category_id'         => $category->id,
                    ],
                    [
                        'price_per_size' => 25.00,
                        'price_per_kg'   => 10.00,
                        'per_piece'      => 5.00,
                    ]
                );
            }
        }
    }
}
