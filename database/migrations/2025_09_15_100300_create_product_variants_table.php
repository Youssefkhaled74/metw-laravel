<?php

use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(Schema::hasTable('product_variants')){
            return;
        }
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class,'product_id')->constrained('products');
            $table->string('sku')->unique();
            $table->foreignIdFor(ProductColor::class,'color_id')->nullable()->constrained('product_colors');
            $table->foreignIdFor(ProductSize::class,'size_id')->nullable()->constrained('product_sizes');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
