<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductShippingAndReturnFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_can_create_product_with_shipping_and_return_sections(): void
    {
        File::ensureDirectoryExists(public_path('storage/products'));

        $vendor = Vendor::factory()->create();
        Sanctum::actingAs($vendor);

        $mainCategory = MainCategory::factory()->create();
        $secondaryMainCategory = MainCategory::factory()->create();
        $category = Category::factory()->create(['main_category_id' => $mainCategory->id]);
        $secondaryCategory = Category::factory()->create(['main_category_id' => $secondaryMainCategory->id]);
        $brand = Brand::factory()->create();

        $response = $this->post('/api/v1/ecommerce/products', [
            'vendor_id' => $vendor->id,
            'main_category_id' => $mainCategory->id,
            'main_category_id_2' => $secondaryMainCategory->id,
            'category_id' => $category->id,
            'category_id_2' => $secondaryCategory->id,
            'brand_id' => $brand->id,
            'name' => 'Shipping Test Product',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'price' => 250.50,
            'stock' => 15,
            'discount_percentage' => 10,
            'discount_start' => now()->subDay()->toDateString(),
            'discount_end' => now()->addDay()->toDateString(),
            'is_active' => true,
            'features' => ['Feature 1', 'Feature 2'],
            'product_info' => ['Info 1', 'Info 2'],
            'translations' => [
                'ar' => [
                    'name' => 'منتج اختبار',
                    'short_description' => 'وصف قصير',
                    'description' => 'وصف كامل',
                ],
            ],
            'shipping_profile' => [
                'shipment_type' => 'box',
                'shipment_description' => 'Handled with care',
                'shipment_dimensions' => '30x20x10',
                'shipment_weight' => '3kg',
                'package_length' => 30,
                'package_width' => 20,
                'package_height' => 10,
                'package_weight' => 3,
                'storage_conditions' => ['dry_place'],
                'delivery_zones' => [1, 2],
                'delivery_options' => ['same_day', 'scheduled'],
            ],
            'return_policy' => [
                'is_returnable' => true,
                'return_fee' => 25,
                'return_validity' => 14,
            ],
            'shipping_fee_policy' => [
                'free_shipping' => 'price',
                'free_shipping_min_order' => 500,
                'free_shipping_price' => 35,
            ],
            'media' => [
                [
                    'type' => 'image',
                    'file' => UploadedFile::fake()->image('product.jpg'),
                    'position' => 0,
                ],
            ],
            'variants' => [
                [
                    'price' => 240,
                    'stock' => 5,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipping_profile.shipment_type', 'box')
            ->assertJsonPath('data.return_policy.is_returnable', true)
            ->assertJsonPath('data.shipping_fee_policy.free_shipping', 'price');

        $product = Product::withoutGlobalScopes()->with([
            'shippingProfile',
            'returnPolicy',
            'shippingFeePolicy',
            'translations',
            'variants',
        ])->firstOrFail();

        $this->assertSame($secondaryMainCategory->id, $product->main_category_id_2);
        $this->assertSame($secondaryCategory->id, $product->category_id_2);
        $this->assertSame('price', $product->free_shipping);
        $this->assertEquals(35, $product->free_shipping_price);
        $this->assertTrue($product->is_returnable);
        $this->assertEquals(25, $product->return_fee);
        $this->assertEquals(30, $product->shippingProfile->package_length);
        $this->assertSame('box', $product->shippingProfile->shipment_type);
        $this->assertTrue($product->returnPolicy->is_returnable);
        $this->assertSame('price', $product->shippingFeePolicy->free_shipping);
        $this->assertCount(1, $product->translations);
        $this->assertCount(1, $product->variants);
    }

    public function test_vendor_can_update_product_shipping_and_return_sections(): void
    {
        File::ensureDirectoryExists(public_path('storage/products'));

        $vendor = Vendor::factory()->create();
        Sanctum::actingAs($vendor);

        $mainCategory = MainCategory::factory()->create();
        $category = Category::factory()->create(['main_category_id' => $mainCategory->id]);
        $brand = Brand::factory()->create();

        $product = Product::factory()->create([
            'vendor_id' => $vendor->id,
            'main_category_id' => $mainCategory->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'free_shipping' => '0',
            'free_shipping_price' => null,
            'is_returnable' => false,
        ]);

        $response = $this->put('/api/v1/ecommerce/products/' . $product->id, [
            'name' => 'Updated Shipping Product',
            'stock' => 22,
            'price' => 300,
            'shipping_profile' => [
                'shipment_type' => 'fragile_box',
                'shipment_description' => 'Keep upright',
                'shipment_dimensions' => '40x30x20',
                'shipment_weight' => '5kg',
                'package_length' => 40,
                'package_width' => 30,
                'package_height' => 20,
                'package_weight' => 5,
                'storage_conditions' => ['cool'],
                'delivery_options' => ['next_day'],
            ],
            'return_policy' => [
                'is_returnable' => true,
                'return_fee' => 15,
                'return_validity' => 7,
            ],
            'shipping_fee_policy' => [
                'free_shipping' => 'available',
                'free_shipping_min_order' => 200,
                'free_shipping_price' => 0,
            ],
            'media' => [
                [
                    'type' => 'image',
                    'file' => UploadedFile::fake()->image('updated.jpg'),
                    'position' => 0,
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipping_profile.package_weight', '5.00')
            ->assertJsonPath('data.return_policy.return_fee', '15.00')
            ->assertJsonPath('data.shipping_fee_policy.free_shipping', 'available');

        $product->refresh();
        $product->load(['shippingProfile', 'returnPolicy', 'shippingFeePolicy']);

        $this->assertSame('available', $product->free_shipping);
        $this->assertEquals(200, $product->free_shipping_min_order);
        $this->assertTrue($product->is_returnable);
        $this->assertSame('fragile_box', $product->shippingProfile->shipment_type);
        $this->assertEquals(7, $product->returnPolicy->return_validity);
        $this->assertSame('available', $product->shippingFeePolicy->free_shipping);
    }
}
