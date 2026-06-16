<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->unsignedBigInteger('shipment_company_id')->nullable()->after('group');

            $table->foreign('shipment_company_id')
                ->references('id')
                ->on('shipment_companies')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropForeign(['shipment_company_id']);
            $table->dropColumn('shipment_company_id');
        });
    }
};
