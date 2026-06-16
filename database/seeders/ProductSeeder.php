<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductTranslation;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure we have at least one vendor to own the products
        $vendor = Vendor::firstOrCreate(
            ['email' => 'vendor1@lasco.test'],
            [
                'name'           => 'Demo Vendor 1',
                'phone'          => '201000000100',
                'password'       => Hash::make('Vendor1234'),
                'address'        => 'Cairo, Egypt',
                'latitude'       => 30.0444,
                'longitude'      => 31.2357,
                'logo'           => null,
                'email_verified' => true,
                'phone_verified' => true,
                'is_active'      => true,
                'country_code'   => '+20',
            ]
        );

        $brands        = Brand::all();
        $mainCategories = MainCategory::all();
        $categories     = Category::all();

        if ($mainCategories->isEmpty() || $categories->isEmpty()) {
            // Main categories & categories should be seeded by MainCategorySeeder & CategorySeeder
            return;
        }

        // Create a few products with realistic relations
        for ($i = 1; $i <= 15; $i++) {
            $mainCategory = $mainCategories->random();

            $category = $categories
                ->where('main_category_id', $mainCategory->id)
                ->random() ?? $categories->random();

            $brand = $brands->isNotEmpty() ? $brands->random() : null;

            $nameEn = $faker->words(3, true);
            $slug   = Str::slug($nameEn . '-' . $i);

            // Decide free shipping mode according to enum: ['0', 'available', 'price']
            $freeShippingMode = $faker->randomElement(['0', 'available', 'price']);
            $freeShippingPrice = $freeShippingMode === 'price'
                ? $faker->randomFloat(2, 100, 500)
                : null;

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'vendor_id'            => $vendor->id,
                    'category_id'          => $category->id,
                    'category_id_2'        => null,
                    'brand_id'             => $brand?->id,
                    'name'                 => $nameEn,
                    'slug'                 => $slug,
                    'sku'                  => 'SKU-' . strtoupper(Str::random(6)),
                    'stock'                => $faker->numberBetween(10, 200),
                    'short_description'    => $faker->sentence(8),
                    'description'          => $faker->paragraph(3),
                    'price'                => $faker->randomFloat(2, 50, 2000),
                    'is_active'            => true,
                    'view_count'           => 0,
                    'sold_count'           => 0,
                    'rating_count'         => 0,
                    'rating'               => 0,
                    'main_category_id'     => $mainCategory->id,
                    'main_category_id_2'   => null,
                    'features'             => ['demo' => 'feature list'],
                    'product_info'         => ['warranty' => '1 year'],
                    'usage_description'    => $faker->sentence(10),
                    'parts_description'    => $faker->sentence(10),
                    'material_description' => $faker->sentence(6),
                    'dimensions'           => '30x20x10 cm',
                    'weight'               => 1.5,
                    'volume'               => 6,
                    'available_sizes'      => ['S', 'M', 'L'],
                    'available_colors'     => ['Red', 'Blue'],
                    'origin_country'       => 'Egypt',
                    'manufacturer'         => 'Demo Manufacturer',
                    'model'                => 'Model ' . strtoupper(Str::random(4)),
                    'expiry_period'        => null,
                    'discount_percentage'  => 0,
                    'discounted_price'     => null,
                    // enum('0','available','price')
                    'free_shipping'        => $freeShippingMode,
                    'free_shipping_min_order' => null,
                    'shipment_type'        => null,
                    'shipment_description' => null,
                    'shipment_dimensions'  => null,
                    'shipment_weight'      => null,
                    'storage_conditions'   => [],
                    'delivery_zones'       => [],
                    'delivery_options'     => [],
                    'subcategories_level1' => null,
                    'subcategory_level2'   => null,
                    'auto_discount_end_date' => null,
                    'free_shipping_price'  => $freeShippingPrice,
                    'package_length'       => null,
                    'package_width'        => null,
                    'package_height'       => null,
                    'package_weight'       => null,
                    'is_returnable'        => true,
                    'return_fee'           => 0,
                    'return_validity'      => 14,
                    'branch_id'            => null,
                    'has_deposit'          => false,
                    'deposit_percentage'   => 0,
                    'piece_type'           => null,
                    'pieces_per_package'   => 1,
                ]
            );

            // Translations
            ProductTranslation::updateOrCreate(
                ['product_id' => $product->id, 'locale' => 'en'],
                [
                    'name'              => $nameEn,
                    'slug'              => $slug,
                    'short_description' => $product->short_description,
                    'description'       => $product->description,
                ]
            );

            ProductTranslation::updateOrCreate(
                ['product_id' => $product->id, 'locale' => 'ar'],
                [
                    'name'              => 'منتج ' . $i,
                    'slug'              => $slug . '-ar',
                    'short_description' => 'وصف قصير للمنتج ' . $i,
                    'description'       => 'وصف تفصيلي للمنتج ' . $i,
                ]
            );

            // Media
            for ($img = 1; $img <= 2; $img++) {
                ProductMedia::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'url'        => "products/demo-{$product->id}-{$img}.jpg",
                    ],
                    [
                        'type'     => \App\Enum\ProductMediaType::IMAGE,
                        'position' => $img,
                        'variant_id' => null,
                    ]
                );
            }
        }
    }
}


