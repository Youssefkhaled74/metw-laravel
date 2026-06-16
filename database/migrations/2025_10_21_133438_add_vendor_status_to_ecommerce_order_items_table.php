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
            if(!Schema::hasColumn('ecommerce_order_items', 'vendor_status')) {
                $table->string('vendor_status')->default('pending')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if(Schema::hasColumn('ecommerce_order_items', 'vendor_status')) {
                $table->dropColumn('vendor_status');
            }
        });
    }
};
