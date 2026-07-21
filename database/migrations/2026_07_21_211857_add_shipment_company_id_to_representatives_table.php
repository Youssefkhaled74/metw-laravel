<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            if (! Schema::hasColumn('representatives', 'shipment_company_id')) {
                $table->foreignId('shipment_company_id')
                    ->nullable()
                    ->after('warehouse_id')
                    ->constrained('shipment_companies')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            if (Schema::hasColumn('representatives', 'shipment_company_id')) {
                $table->dropForeign(['shipment_company_id']);
                $table->dropColumn('shipment_company_id');
            }
        });
    }
};
