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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'final_price')) {
                $table->decimal('final_price', 10, 2)->after('discount_price')->nullable();
            }
        });
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'final_price')) {
                $table->decimal('final_price', 10, 2)->after('discount_price')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('final_price');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('final_price');
        });
    }
};
