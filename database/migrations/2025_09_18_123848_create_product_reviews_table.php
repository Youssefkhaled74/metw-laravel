<?php

use App\Models\EcommerceOrderItem;
use App\Models\Product;
use App\Models\User;
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
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();

                $table->foreignIdFor(Product::class)->constrained();
                $table->foreignIdFor(User::class)->constrained();
                $table->foreignIdFor(EcommerceOrderItem::class)->constrained();

                $table->tinyInteger('rating');
                $table->text('comment')->nullable();

                $table->timestamps();
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
