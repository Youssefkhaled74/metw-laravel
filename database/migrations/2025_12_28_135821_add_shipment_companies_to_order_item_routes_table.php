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
        Schema::table('order_item_routes', function (Blueprint $table) {
            // Add columns for shipment company ids
            $table->foreignId('pickup_company_id')
                  ->nullable()
                  ->after('order_item_id')
                  ->constrained('shipment_companies')
                  ->cascadeOnDelete();

            $table->foreignId('dropoff_company_id')
                  ->nullable()
                  ->after('pickup_company_id')
                  ->constrained('shipment_companies')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_item_routes', function (Blueprint $table) {
            $table->dropForeign(['pickup_company_id']);
            $table->dropColumn('pickup_company_id');

            $table->dropForeign(['dropoff_company_id']);
            $table->dropColumn('dropoff_company_id');
        });
    }
};
