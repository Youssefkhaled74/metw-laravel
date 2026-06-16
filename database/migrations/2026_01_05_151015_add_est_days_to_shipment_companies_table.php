<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipment_companies', function (Blueprint $table) {
            $table->unsignedInteger('est_days')
                  ->nullable()
                  ->after('price_per_km'); // adjust position if needed
        });
    }

    public function down(): void
    {
        Schema::table('shipment_companies', function (Blueprint $table) {
            $table->dropColumn('est_days');
        });
    }
};
