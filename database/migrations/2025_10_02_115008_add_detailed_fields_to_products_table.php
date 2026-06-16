<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
        // روابط الأقسام
        $table->unsignedBigInteger('main_category_id_2')->nullable()->after('main_category_id');
        $table->unsignedBigInteger('category_id_2')->nullable()->after('category_id');

        // أوصاف ومعلومات
        $table->text('features')->nullable(); // JSON: [feature1, feature2, feature3]
        $table->text('product_info')->nullable(); // JSON: [info1, info2, info3]
        $table->longText('usage_description')->nullable();
        $table->longText('parts_description')->nullable();
        $table->longText('material_description')->nullable();

        // مواصفات وأبعاد
        $table->string('dimensions')->nullable();
        $table->string('weight')->nullable();
        $table->string('volume')->nullable();
        $table->string('available_sizes')->nullable(); // CSV or JSON
        $table->string('available_colors')->nullable(); // CSV or JSON


        // بلد وشركة
        $table->string('origin_country')->nullable();
        $table->string('manufacturer')->nullable();
        $table->string('model')->nullable();

        // صلاحية
        $table->string('expiry_period')->nullable();

        // خصومات
        $table->decimal('discount_percentage', 5, 2)->default(0);
        $table->decimal('discounted_price', 10, 2)->nullable();
        $table->date('discount_start')->nullable();
        $table->date('discount_end')->nullable();

        // شحن
        $table->boolean('free_shipping')->default(false);
        $table->decimal('free_shipping_min_order', 10, 2)->nullable();
        $table->string('shipment_type')->nullable();
        $table->string('shipment_description')->nullable();
        $table->string('shipment_dimensions')->nullable();
        $table->string('shipment_weight')->nullable();
        $table->json('storage_conditions')->nullable();
        $table->json('delivery_zones')->nullable();
        $table->json('delivery_options')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'main_category_id_2', 'category_id_2', 'features', 'product_info',
                'usage_description', 'parts_description', 'material_description',
                'dimensions', 'weight', 'volume', 'available_sizes', 'available_colors',
                'color_images', 'extra_images', 'video', 'origin_country', 'manufacturer',
                'brand_name', 'model', 'expiry_period', 'discount_percentage', 'discounted_price',
                'discount_start', 'discount_end', 'free_shipping', 'free_shipping_min_order',
                'shipment_type', 'shipment_description', 'shipment_dimensions', 'shipment_weight',
                'storage_conditions', 'delivery_zones', 'delivery_options'
            ]);
        });
    }
};
