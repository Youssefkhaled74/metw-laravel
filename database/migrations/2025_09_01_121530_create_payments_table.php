<?php

use App\Enum\PaymentStatus;
use App\Models\Order;
use App\Models\PromoCode;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->string('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_price')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default(PaymentStatus::PENDING);
            $table->foreignIdFor(PromoCode::class)->nullable()->constrained()->nullOnDelete();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
