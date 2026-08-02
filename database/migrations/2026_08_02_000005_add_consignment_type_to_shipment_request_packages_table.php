<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_request_packages', function (Blueprint $table) {
            if (! Schema::hasColumn('shipment_request_packages', 'consignment_type_id')) {
                $table->foreignId('consignment_type_id')
                    ->nullable()
                    ->after('package_type')
                    ->constrained('consignment_types')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipment_request_packages', function (Blueprint $table) {
            if (Schema::hasColumn('shipment_request_packages', 'consignment_type_id')) {
                $table->dropConstrainedForeignId('consignment_type_id');
            }
        });
    }
};
