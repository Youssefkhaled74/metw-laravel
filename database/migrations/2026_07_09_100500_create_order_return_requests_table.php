<?php

use App\Enum\ReturnRequestStatus;
use App\Models\Order;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Representative::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->foreign('reason_id')->references('id')->on('return_reasons')->nullOnDelete();
            $table->text('custom_reason_text')->nullable();
            $table->string('status', 30)->default(ReturnRequestStatus::REQUESTED->value);
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('whatsapp_deep_link')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->decimal('shipping_fees', 12, 2)->nullable();
            $table->decimal('return_fees', 12, 2)->nullable();
            $table->decimal('net_refund', 12, 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_requests');
    }
};
