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
        Schema::table('orders', function (Blueprint $table) {
            if(Schema::hasColumn('orders', 'shipment_compnay_id')) {
                $table->foreignIdFor(ShipmentCompany::class)->constrained()->nullOnDelete();
            }
            if(!Schema::hasColumn('orders','paid_amount')){
                $table->decimal('paid_amount', 10, 2)->after('total_price')->default(0);
            }
            if(!Schema::hasColumn('orders','remaining_amount')){
                $table->decimal('remaining_amount', 10, 2)->after('paid_amount')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if(Schema::hasColumn('orders','paid_amount')){
                $table->dropColumn('paid_amount');
            }
            if(Schema::hasColumn('orders','remaining_amount')){
                $table->dropColumn('remaining_amount');
            }
        });
    }
};
