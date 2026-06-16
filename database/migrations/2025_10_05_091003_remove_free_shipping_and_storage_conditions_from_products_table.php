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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'free_shipping')) {
                $table->dropColumn('free_shipping');
            }
            if (Schema::hasColumn('products', 'storage_conditions')) {
                $table->dropColumn('storage_conditions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Recreate the columns if you roll back the migration
            $table->enum('free_shipping', ['0', 'available', 'price'])->default('0')->after('discount_end');
            $table->json('storage_conditions')->nullable()->after('free_shipping');
        });
    }
};
