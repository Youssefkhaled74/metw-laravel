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

            // 1. Core lookups (no dependencies)
            AdminSeeder::class,
            SettingSeeder::class,
            ConfigSeeder::class,
            PackageTypeSeeder::class,
            ConsignmentTypeSeeder::class,
            DeliveryTypeSeeder::class,
            SizeSeeder::class,
            ProductSizeSeeder::class,
            ProductColorSeeder::class,
            CancelReasonSeeder::class,
            ContactAdminSeeder::class,
            WhatsappTemplateSeeder::class,
            BannarSeeder::class,
            PageSeeder::class,
            BrandSeeder::class,

            // 2. Location hierarchy
            StatesSeeder::class,        // creates Country + State
            CitiesTableSeeder::class,
            ZonesTableSeeder::class,
            LocationSeeder::class,      // locations table

            // 3. Auth
            UserSeeder::class,
            RolePermissionSeeder::class,

            // 4. Catalog
            MainCategorySeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,

            // 5. Commerce / Users
            UserAddressSeeder::class,
            WalletSeeder::class,
            PromoCodeSeeder::class,

            // 6. Vendors
            VendorSeeder::class,
            VendorBranchSeeder::class,
            VendorCommissionSeeder::class,

            // 7. Shipment companies
            ShipmentCompanySeeder::class,
            ShipmentCommissionSeeder::class,
            ShipmentLocationSeeder::class,
            CompanyCoverageSeeder::class,
            ShipmentCompanyCategoryPriceSeeder::class,

            // 8. Reviews (requires orders to exist - seed separately)
            // ReviewSeeder::class,
            // ProductReviewSeeder::class,
        ]);
    }
}
