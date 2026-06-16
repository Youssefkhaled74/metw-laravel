<?php

use App\Models\User;
use App\Models\EcommerceOrder;
use App\Models\UserAddress;
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
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(EcommerceOrder::class)->constrained()->cascadeOnDelete();
            $table->string('return_number')->unique();
            $table->string('status')->default('requested');
            $table->string('reason');
            $table->text('other_reason')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->foreignIdFor(UserAddress::class, 'pickup_address_id')->nullable()->constrained('user_addresses');
            $table->string('pickup_phone')->nullable();
            $table->date('pickup_date')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
