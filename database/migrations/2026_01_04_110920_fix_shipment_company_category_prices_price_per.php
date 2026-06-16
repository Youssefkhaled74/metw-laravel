<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipment_company_category_prices', function (Blueprint $table) {
            // ✅ Rename column price_per_km to price_per_size
            if (Schema::hasColumn('shipment_company_category_prices', 'price_per_km')) {
                $table->renameColumn('price_per_km', 'price_per_size');
            }

            // ✅ Ensure per_piece exists
            if (!Schema::hasColumn('shipment_company_category_prices', 'per_piece')) {
                $table->decimal('per_piece', 10, 2)->nullable()->after('price_per_kg');
            }

            // ✅ Ensure price_per_kg exists
            if (!Schema::hasColumn('shipment_company_category_prices', 'price_per_kg')) {
                $table->decimal('price_per_kg', 10, 2)->nullable()->after('price_per_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipment_company_category_prices', function (Blueprint $table) {
            // revert column name
            if (Schema::hasColumn('shipment_company_category_prices', 'price_per_size')) {
                $table->renameColumn('price_per_size', 'price_per_km');
            }
        });
    }
};
