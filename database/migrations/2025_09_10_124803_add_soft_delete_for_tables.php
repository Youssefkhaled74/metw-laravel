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
            $table->softDeletes();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('shipment_companies', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('shipment_companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
