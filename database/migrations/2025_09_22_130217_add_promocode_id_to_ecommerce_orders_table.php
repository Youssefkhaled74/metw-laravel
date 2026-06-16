<?php

use App\Models\PromoCode;
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
            if (!Schema::hasColumn('ecommerce_orders', 'promo_code_id')) {
                $table->foreignIdFor(PromoCode::class)->nullable()->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_orders', 'promo_code_id')) {
                $table->dropForeign(['promo_code_id']);
                $table->dropColumn('promo_code_id');
            }
        });
    }
};
