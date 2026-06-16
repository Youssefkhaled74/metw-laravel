<?php

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\Package;
use App\Models\ShipmentCompany;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Package::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ShipmentCompany::class)->constrained()->cascadeOnDelete();
            $table->date('est_date')->nullable();
            $table->decimal('est_price', 10, 2)->nullable();
            $table->string('status')->default(OrderStatus::PENDING);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
