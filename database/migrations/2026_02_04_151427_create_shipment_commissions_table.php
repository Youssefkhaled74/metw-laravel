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
        Schema::create('shipment_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_company_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('annual_subscription', 10, 2)->default(100);
            $table->decimal('shipment_commission_percent', 5, 2)->default(5);
            $table->decimal('shipment_commission_min', 10, 2)->default(3);
            $table->decimal('annual_target', 10, 2)->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_commissions');
    }
};
