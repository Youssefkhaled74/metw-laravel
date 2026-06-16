<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        /** ----------------------------------------
         * DROP main_category_id safely
         * -------------------------------------- */
        if (Schema::hasColumn('shipment_company_category_prices', 'main_category_id')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->dropForeign(['main_category_id']);
                $table->dropColumn('main_category_id');
            });
        }

        /** ----------------------------------------
         * ADD category_id safely
         * -------------------------------------- */
        if (!Schema::hasColumn('shipment_company_category_prices', 'category_id')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->after('shipment_company_id');

                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->cascadeOnDelete();
            });
        }

        /** ----------------------------------------
         * ADD per_piece safely
         * -------------------------------------- */
        if (!Schema::hasColumn('shipment_company_category_prices', 'per_piece')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->decimal('per_piece', 10, 2)->nullable()->after('price_per_kg');
            });
        }
    }

    public function down(): void
    {
        /** ----------------------------------------
         * DROP per_piece
         * -------------------------------------- */
        if (Schema::hasColumn('shipment_company_category_prices', 'per_piece')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->dropColumn('per_piece');
            });
        }

        /** ----------------------------------------
         * DROP category_id
         * -------------------------------------- */
        if (Schema::hasColumn('shipment_company_category_prices', 'category_id')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        /** ----------------------------------------
         * RESTORE main_category_id
         * -------------------------------------- */
        if (!Schema::hasColumn('shipment_company_category_prices', 'main_category_id')) {
            Schema::table('shipment_company_category_prices', function (Blueprint $table) {
                $table->unsignedBigInteger('main_category_id')->after('shipment_company_id');
                $table->foreign('main_category_id')
                    ->references('id')
                    ->on('main_categories')
                    ->cascadeOnDelete();
            });
        }
    }
};
