<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            $columns = [
                'shipping_type' => ['string', ['default' => 'direct_and_multi', 'after' => 'status']],
                'courier_category' => ['string', ['nullable' => true, 'after' => 'shipping_type']],
                'total_weight' => ['decimal:10,2', ['nullable' => true, 'after' => 'courier_category']],
                'total_volume' => ['decimal:10,2', ['nullable' => true, 'after' => 'total_weight']],
                'has_village' => ['boolean', ['default' => false, 'after' => 'total_volume']],
                'dispatch_state' => ['string', ['default' => 'pending', 'after' => 'has_village']],
                'dispatch_note' => ['string', ['nullable' => true, 'after' => 'dispatch_state']],
            ];

            foreach ($columns as $column => [$type, $options]) {
                if (Schema::hasColumn('shipment_requests', $column)) {
                    continue;
                }

                if ($type === 'decimal:10,2') {
                    $table->decimal($column, 10, 2)->nullable()->after($options['after'] ?? 'status');
                } elseif ($type === 'boolean') {
                    $table->boolean($column)->default($options['default'] ?? false)->after($options['after'] ?? 'status');
                } else {
                    $table->string($column, 50)->nullable()->after($options['after'] ?? 'status');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            foreach (['shipping_type', 'courier_category', 'total_weight', 'total_volume', 'has_village', 'dispatch_state', 'dispatch_note'] as $column) {
                if (Schema::hasColumn('shipment_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
