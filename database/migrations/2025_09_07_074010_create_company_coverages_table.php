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
        if (!Schema::hasTable('company_coverages')) {
            Schema::create('company_coverages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shipment_company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();

                //can do pickup or delivery or both
                $table->boolean('pickup_available')->default(true);
                $table->boolean('delivery_available')->default(true);

                $table->unsignedTinyInteger('eta_min_days')->nullable();
                $table->unsignedTinyInteger('eta_max_days')->nullable();
                $table->decimal('eta_price', 10, 2)->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->unique(['shipment_company_id', 'location_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('company_coverages')) {
            Schema::dropIfExists('company_coverages');
        }
    }
};
