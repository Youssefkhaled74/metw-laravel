<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('return_request_items', function (Blueprint $table) {

            $table->decimal('item_subtotal', 12, 2)->default(0);

            // Vendor
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->decimal('vendor_refund_commission', 12, 2)->default(0);

            // Shipment
            $table->unsignedBigInteger('shipment_company_id')->nullable();
            $table->decimal('return_shipping_cost', 12, 2)->default(0);
            $table->decimal('shipment_commission', 12, 2)->default(0);
            $table->decimal('shipment_net', 12, 2)->default(0);

            // Final
            $table->decimal('customer_refund_amount', 12, 2)->default(0);

            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
            $table->foreign('shipment_company_id')->references('id')->on('shipment_companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_request_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_subtotal',
                'vendor_id',
                'vendor_refund_commission',
                'shipment_company_id',
                'return_shipping_cost',
                'shipment_commission',
                'shipment_net',
                'customer_refund_amount',
            ]);
        });
    }
};
