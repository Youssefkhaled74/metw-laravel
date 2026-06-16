<?php

use App\Enum\PaymentStatus;
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
            if(!Schema::hasColumn('ecommerce_orders', 'payment_status')) {
                $table->string('payment_status')->after('payment_method')->default(PaymentStatus::UNPAID->value);
            }
            //final price
            if(!Schema::hasColumn('ecommerce_orders', 'final_price')) {
                $table->decimal('final_price', 10, 2)->after('remaining_amount')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            if(Schema::hasColumn('ecommerce_orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if(Schema::hasColumn('ecommerce_orders', 'final_price')) {
                $table->dropColumn('final_price');
            }
        });
    }
};
