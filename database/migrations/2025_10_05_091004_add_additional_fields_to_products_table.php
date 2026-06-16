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
            // Classifications
            $table->string('subcategories_level1')->nullable();
            $table->string('subcategory_level2')->nullable();

            //  discount
            $table->date('auto_discount_end_date')->nullable();

            // Shipping
            $table->enum('free_shipping', ['0', 'available', 'price'])->default('0');
            $table->decimal('free_shipping_price', 10, 2)->nullable();

            // Package dimensions
            $table->decimal('package_length', 10, 2)->nullable();
            $table->decimal('package_width', 10, 2)->nullable();
            $table->decimal('package_height', 10, 2)->nullable();
            $table->decimal('package_weight', 10, 2)->nullable();

            // Storage conditions (JSON)
            $table->json('storage_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'subcategories_level1',
                'subcategory_level2',
                'colors',
                'auto_discount_end_date',
                'free_shipping_price',
                'package_length',
                'package_width',
                'package_height',
                'package_weight',
            ]);
        });
    }
};
