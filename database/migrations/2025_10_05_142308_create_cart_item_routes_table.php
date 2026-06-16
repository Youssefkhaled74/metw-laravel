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
        Schema::create('cart_item_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained('cart_items')->onDelete('cascade');
            $table->foreignId('pickup_company_id')->nullable()->constrained('shipment_companies');
            $table->foreignId('dropoff_company_id')->nullable()->constrained('shipment_companies');
            $table->json('pickup_address');
            $table->json('dropoff_address');
            $table->json('handoff_point')->nullable();
            $table->json('legs')->nullable(); // كل leg فيها بيانات شركة وتسعيرة
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->boolean('is_split')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_routes');
    }
};
