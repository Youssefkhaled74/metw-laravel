<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MainCategory;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorProductFilterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_product_filters_only_show_subcategories_for_selected_main_category(): void
    {
        app()->setLocale('en');

        $vendor = Vendor::factory()->create();
        $this->actingAs($vendor, 'vendor');

        $mainCategory = MainCategory::create([
            'name' => 'Main A',
            'is_active' => true,
        ]);

        $otherMainCategory = MainCategory::create([
            'name' => 'Main B',
            'is_active' => true,
        ]);

        $selectedCategory = Category::create([
            'name' => 'Category A',
            'main_category_id' => $mainCategory->id,
            'is_active' => true,
        ]);

        $otherCategory = Category::create([
            'name' => 'Category B',
            'main_category_id' => $otherMainCategory->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('vendor.products', [
            'main_category_id' => $mainCategory->id,
        ]));

        $response->assertOk();
        $response->assertSee($selectedCategory->name);
        $response->assertDontSee($otherCategory->name);
    }
}
