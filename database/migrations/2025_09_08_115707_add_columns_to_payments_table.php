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
        Schema::table('payments', function (Blueprint $table) {
            if(Schema::hasColumn('payments', 'amount')){
                $table->dropColumn('amount');
            }
            if (!Schema::hasColumn('payments', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('transaction_id')->nullable();
            }
            if (!Schema::hasColumn('payments', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->after('total_amount')->nullable();
            }
            if (!Schema::hasColumn('payments', 'remaining_amount')) {
                $table->decimal('remaining_amount', 10, 2)->after('paid_amount')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if(Schema::hasColumn('payments', 'total_amount')){
                $table->dropColumn('total_amount');
            }
            if(Schema::hasColumn('payments', 'paid_amount')){
                $table->dropColumn('paid_amount');
            }
            if(Schema::hasColumn('payments', 'remaining_amount')){
                $table->dropColumn('remaining_amount');
            }
        });
    }
};
