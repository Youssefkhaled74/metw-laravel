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
        Schema::create('shipment_company_category_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('main_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_per_km', 10, 2)->nullable();
            $table->decimal('price_per_kg', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['shipment_company_id', 'main_category_id'], 'unique_company_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_company_category_prices');
    }
};
