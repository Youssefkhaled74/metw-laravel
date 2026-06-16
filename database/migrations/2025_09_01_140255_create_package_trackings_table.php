<?php

use App\Enum\OrderStatus;
use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('package_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Package::class)->constrained();
            $table->foreignIdFor(OrderItem::class)->constrained();
            $table->string('status')->default(OrderStatus::PENDING);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable();
            $table->timestampsTz();
            
            $table->index(['package_id', 'occurred_at']);
            $table->index(['order_item_id', 'status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_tracking');
    }
};
