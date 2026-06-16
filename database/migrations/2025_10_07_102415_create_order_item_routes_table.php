<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');

            // From location details
            $table->json('from_address')->nullable();
            $table->decimal('from_latitude', 10, 8)->nullable();
            $table->decimal('from_longitude', 11, 8)->nullable();
            $table->foreignId('from_city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('from_state_id')->nullable()->constrained('states')->onDelete('set null');
            $table->foreignId('from_zone_id')->nullable()->constrained('zones')->onDelete('set null');

            // To location details
            $table->json('to_address')->nullable();
            $table->decimal('to_latitude', 10, 8)->nullable();
            $table->decimal('to_longitude', 11, 8)->nullable();
            $table->foreignId('to_city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('to_state_id')->nullable()->constrained('states')->onDelete('set null');
            $table->foreignId('to_zone_id')->nullable()->constrained('zones')->onDelete('set null');

            // Leg details
            $table->enum('leg_type', ['direct', 'pickup', 'dropoff'])->default('direct');
            $table->integer('leg_order')->default(1);
            $table->decimal('distance', 10, 2)->nullable()->comment('Distance in km');
            $table->decimal('cost', 10, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_item_id');
            $table->index(['order_item_id', 'leg_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_routes');
    }
};
