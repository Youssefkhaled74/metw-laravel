<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $columns = [
                'shipping_type' => 'direct_and_multi',
                'courier_category' => null,
                'has_village' => false,
                'dispatch_state' => 'pending',
                'dispatch_note' => null,
            ];

            foreach ($columns as $column => $default) {
                if (Schema::hasColumn('order_items', $column)) {
                    continue;
                }

                if (is_bool($default)) {
                    $table->boolean($column)->default($default)->after('status');
                } elseif (is_null($default)) {
                    $table->string($column, 50)->nullable()->after('status');
                } else {
                    $table->string($column, 50)->default($default)->after('status');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['shipping_type', 'courier_category', 'has_village', 'dispatch_state', 'dispatch_note'] as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
