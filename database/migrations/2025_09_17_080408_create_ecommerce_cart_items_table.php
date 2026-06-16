<?php

use App\Models\EcommerceCart;
use App\Models\Product;
use App\Models\ProductVariant;
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
        Schema::create('ecommerce_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EcommerceCart::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(ProductVariant::class, 'variant_id')->nullable()->constrained('product_variants');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_cart_items');
    }
};
