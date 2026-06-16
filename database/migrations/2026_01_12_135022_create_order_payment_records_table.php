<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecommerce_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ecommerce_order_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamps();

            // Short custom index name
            $table->index(
                ['ecommerce_order_id', 'ecommerce_order_item_id'],
                'order_pay_rec_order_item_idx'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_payment_records');
    }
};
