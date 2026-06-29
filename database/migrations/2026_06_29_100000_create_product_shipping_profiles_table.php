<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_shipping_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('shipment_type')->nullable();
            $table->text('shipment_description')->nullable();
            $table->string('shipment_dimensions')->nullable();
            $table->string('shipment_weight')->nullable();
            $table->decimal('package_length', 10, 2)->nullable();
            $table->decimal('package_width', 10, 2)->nullable();
            $table->decimal('package_height', 10, 2)->nullable();
            $table->decimal('package_weight', 10, 2)->nullable();
            $table->json('storage_conditions')->nullable();
            $table->json('delivery_zones')->nullable();
            $table->json('delivery_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_shipping_profiles');
    }
};
