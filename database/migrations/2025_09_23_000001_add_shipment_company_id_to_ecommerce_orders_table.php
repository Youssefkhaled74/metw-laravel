<?php

use App\Models\ShipmentCompany;
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
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_orders', 'shipment_company_id')) {
                $table->foreignIdFor(ShipmentCompany::class)->nullable()->after('user_id')->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_orders', 'shipment_company_id')) {
                $table->dropConstrainedForeignIdFor(ShipmentCompany::class);
            }
        });
    }
};
