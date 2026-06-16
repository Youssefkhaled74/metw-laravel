<?php

use App\Enum\OrderStatus;
use App\Models\Cart;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Cart::class)->nullable()->constrained()->cascadeOnDelete('set null');
            $table->string('order_number')->unique();
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('shipping_price', 10, 2)->nullable();
            $table->string('status')->default(OrderStatus::PENDING);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
