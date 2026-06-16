<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_company_category_prices', function (Blueprint $table) {
            // Drop foreign key first (replace with your actual FK name)
            $table->dropForeign(['shipment_company_id']);

            // Drop the column
            $table->dropColumn('shipment_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_company_category_prices', function (Blueprint $table) {
            // Re-add the column
            $table->unsignedBigInteger('shipment_company_id');

            // Recreate the foreign key if needed (replace with correct table/column)
            $table->foreign('shipment_company_id')
                ->references('id')
                ->on('shipment_companies')
                ->onDelete('cascade');
        });
    }
};
