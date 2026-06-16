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
        Schema::table('return_requests', function (Blueprint $table) {

            // Vendor
            $table->decimal('vendor_refund_commission_total', 12, 2)->default(0);
            $table->decimal('vendor_deduction_total', 12, 2)->default(0);

            // Shipment
            $table->decimal('return_shipping_total', 12, 2)->default(0);
            $table->decimal('shipment_commission_total', 12, 2)->default(0);
            $table->decimal('shipment_net_total', 12, 2)->default(0);

            // Who paid shipping
            $table->enum('shipping_paid_by', ['customer','vendor','platform'])
                ->default('customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_refund_commission_total',
                'vendor_deduction_total',
                'return_shipping_total',
                'shipment_commission_total',
                'shipment_net_total',
                'shipping_paid_by',
            ]);
        });
    }
};
