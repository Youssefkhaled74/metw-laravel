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
        Schema::table('ecommerce_cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_cart_items', 'product_discount')) {
                $table->decimal('product_discount', 8, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('ecommerce_cart_items', 'final_price')) {
                $table->decimal('final_price', 8, 2)->default(0)->after('product_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_cart_items', 'product_discount')) {
                $table->dropColumn('product_discount');
            }
            if (Schema::hasColumn('ecommerce_cart_items', 'final_price')) {
                $table->dropColumn('final_price');
            }
        });
    }
};
