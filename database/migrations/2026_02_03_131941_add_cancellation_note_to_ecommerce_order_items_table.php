<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            $table->text('cancellation_note')->nullable()->after('vendor_status');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_note');
        });
    }

    public function down()
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            $table->dropColumn(['cancellation_note', 'cancelled_at']);
        });
    }
};
