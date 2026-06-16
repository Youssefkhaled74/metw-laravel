<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Location;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            // AdminSeeder::class,
            UserSeeder::class,

            // Locations (countries, states, cities, zones)
            StatesSeeder::class,
            CitiesTableSeeder::class,
            ZonesTableSeeder::class,

            // Catalog structure
            MainCategorySeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,

            // Colors & sizes
            ProductColorSeeder::class,
            ProductSizeSeeder::class,
            SizeSeeder::class,

            // Shipment-related lookup tables
            ConsignmentTypeSeeder::class,
            DeliveryTypeSeeder::class,

            // Content & UI
            PageSeeder::class,
            BannarSeeder::class,

            // Products
            // ProductSeeder::class,
        ]);
    }
}
