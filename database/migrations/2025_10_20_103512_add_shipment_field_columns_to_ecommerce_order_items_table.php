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
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_order_items', 'shipment_company_id')) {
                $table->foreignIdFor(ShipmentCompany::class, 'shipment_company_id')->nullable()->after('total_price')->constrained('shipment_companies');
            }
            if (!Schema::hasColumn('ecommerce_order_items', 'shipment_price')) {
                $table->decimal('shipment_price', 8, 2)->default(0)->after('shipment_company_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_order_items', 'shipment_company_id')) {
                $table->dropConstrainedForeignIdFor(ShipmentCompany::class, 'shipment_company_id');
            }
            if (Schema::hasColumn('ecommerce_order_items', 'shipment_price')) {
                $table->dropColumn('shipment_price');
            }
        });
    }
};
