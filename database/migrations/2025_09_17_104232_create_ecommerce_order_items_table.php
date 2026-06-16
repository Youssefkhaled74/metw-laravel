<?php

use App\Models\EcommerceOrder;
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
        if (!Schema::hasTable('ecommerce_order_items')) {
            Schema::create('ecommerce_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(EcommerceOrder::class)->constrained();
                $table->foreignIdFor(Product::class)->constrained();
                $table->foreignIdFor(ProductVariant::class)->nullable()->constrained();
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_items');
    }
};
