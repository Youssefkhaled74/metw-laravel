<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_types', function (Blueprint $table) {
            if (! Schema::hasColumn('consignment_types', 'shipping_type')) {
                $table->string('shipping_type', 50)
                    ->default('direct_and_multi')
                    ->after('code');
            }

            if (! Schema::hasColumn('consignment_types', 'courier_category')) {
                $table->string('courier_category', 30)
                    ->nullable()
                    ->after('shipping_type');
            }

            if (! Schema::hasColumn('consignment_types', 'max_weight')) {
                $table->decimal('max_weight', 10, 2)
                    ->nullable()
                    ->after('courier_category');
            }

            if (! Schema::hasColumn('consignment_types', 'max_volume')) {
                $table->decimal('max_volume', 10, 2)
                    ->nullable()
                    ->after('max_weight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consignment_types', function (Blueprint $table) {
            foreach (['shipping_type', 'courier_category', 'max_weight', 'max_volume'] as $column) {
                if (Schema::hasColumn('consignment_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
