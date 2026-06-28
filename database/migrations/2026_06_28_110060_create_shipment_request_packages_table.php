<?php

use App\Models\ShipmentRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_request_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ShipmentRequest::class)->constrained()->cascadeOnDelete();
            $table->string('package_name');
            $table->string('package_type', 100)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('declared_value', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('shipment_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_request_packages');
    }
};
