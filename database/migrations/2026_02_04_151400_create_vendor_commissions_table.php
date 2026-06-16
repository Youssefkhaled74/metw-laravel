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
        Schema::create('vendor_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('annual_subscription', 10, 2)->default(1000);
            $table->decimal('order_commission_percent', 5, 2)->default(5);
            $table->decimal('order_commission_min', 10, 2)->default(5);
            $table->decimal('annual_target_commission', 10, 2)->default(1000);
            $table->decimal('refund_fee_percent', 5, 2)->default(0.50);
            $table->decimal('refund_fee_min', 10, 2)->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_commissions');
    }
};
