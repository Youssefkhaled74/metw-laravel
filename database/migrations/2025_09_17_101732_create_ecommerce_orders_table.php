<?php

use App\Enum\OrderStatus;
use App\Models\EcommerceCart;
use App\Models\User;
use App\Models\UserAddress;
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
        if (!Schema::hasTable('ecommerce_orders')) {
            Schema::create('ecommerce_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
                $table->foreignIdFor(UserAddress::class)->nullable()->constrained();
                $table->foreignIdFor(EcommerceCart::class)->constrained()->cascadeOnDelete();

                $table->string('order_number')->unique()->nullable();
                $table->string('tracking_number')->unique()->nullable();
                $table->string('phone')->nullable();

                $table->decimal('subtotal', 10, 2);
                $table->decimal('shipping_price', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);

                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->decimal('remaining_amount', 10, 2)->default(0);

                $table->string('payment_method')->nullable();
                $table->string('status')->default(OrderStatus::PENDING->value);

                $table->date('estimated_delivery_from')->nullable();
                $table->date('estimated_delivery_to')->nullable();
                $table->date('actual_delivery_date')->nullable();

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
        Schema::dropIfExists('ecommerce_orders');
    }
};
