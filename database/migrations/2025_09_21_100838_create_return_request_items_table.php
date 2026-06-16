<?php

use App\Models\ReturnRequest;
use App\Models\EcommerceOrderItem;
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
        Schema::create('return_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReturnRequest::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EcommerceOrderItem::class)->constrained()->cascadeOnDelete();
            $table->integer('return_quantity');
            $table->decimal('return_price', 10, 2);
            $table->text('return_reason')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_request_items');
    }
};
