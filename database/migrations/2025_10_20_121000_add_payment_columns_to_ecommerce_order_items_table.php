<?php

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
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_order_items', 'final_price')) {
                $table->decimal('final_price', 10, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('ecommerce_order_items', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('final_price');
            }
            if (!Schema::hasColumn('ecommerce_order_items', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->default(0)->after('paid_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_order_items', 'remaining_amount')) {
                $table->dropColumn('remaining_amount');
            }
            if (Schema::hasColumn('ecommerce_order_items', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            if (Schema::hasColumn('ecommerce_order_items', 'final_price')) {
                $table->dropColumn('final_price');
            }
        });
    }
};


