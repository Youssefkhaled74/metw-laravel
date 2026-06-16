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
            if(!Schema::hasColumn('orders', 'shipment_company_id')) {
                $table->foreignIdFor(ShipmentCompany::class)->nullable()->after('cart_id')->constrained();
            }
            if(Schema::hasColumn('orders', 'shipping_price')) {
                $table->dropColumn('shipping_price');
            }
            if(Schema::hasColumn('orders', 'cart_id')) {
                $table->dropConstrainedForeignId('cart_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if(Schema::hasColumn('orders', 'shipment_company_id')) {
                $table->dropConstrainedForeignIdFor(ShipmentCompany::class);
            }
            $table->decimal('shipping_price', 10, 2)->after('total_price')->default(0);
        });
    }
};
