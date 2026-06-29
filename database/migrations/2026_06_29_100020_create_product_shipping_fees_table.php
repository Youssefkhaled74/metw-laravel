<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_shipping_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->unique()->constrained()->cascadeOnDelete();
            $table->enum('free_shipping', ['0', 'available', 'price'])->default('0');
            $table->decimal('free_shipping_min_order', 10, 2)->nullable();
            $table->decimal('free_shipping_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_shipping_fees');
    }
};
