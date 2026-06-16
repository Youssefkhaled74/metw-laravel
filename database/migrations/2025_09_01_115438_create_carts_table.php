<?php

use App\Enum\CartStatus;
use App\Models\Order;
use App\Models\Package;
use App\Models\ShipmentCompany;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->integer('items_count')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('item_total_price',10,2)->default(0);

            $table->string('status')->default(CartStatus::OPEN); // open|checked_out|abandoned
            $table->timestampsTz();
            $table->index(['user_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
