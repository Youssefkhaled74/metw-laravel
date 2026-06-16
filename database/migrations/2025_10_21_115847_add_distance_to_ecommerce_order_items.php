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
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if(!Schema::hasColumn('ecommerce_order_items', 'shipment_price_company')) {
                $table->decimal('shipment_price_company',10,2)->default(0)->after('shipment_price');
            }
            if(!Schema::hasColumn('ecommerce_order_items', 'distance')) {
                $table->decimal('distance', 10, 2)->default(0)->after('shipment_price_company');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if(Schema::hasColumn('ecommerce_order_items', 'shipment_price_company')){
                $table->dropColumn('shipment_price_company');
            }
            if(Schema::hasColumn('ecommerce_order_items', 'distance')){
                $table->dropColumn('distance');
            }
        });
    }
};
