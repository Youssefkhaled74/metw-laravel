<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('warehouses', 'warehouse_number')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->string('warehouse_number')->nullable()->unique()->after('id');
            });
        }

        $warehouses = DB::table('warehouses')
            ->select('id')
            ->orderBy('id')
            ->get();

        foreach ($warehouses as $index => $warehouse) {
            DB::table('warehouses')
                ->where('id', $warehouse->id)
                ->update([
                    'warehouse_number' => 'WAR-' . str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('warehouses', 'warehouse_number')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropUnique(['warehouse_number']);
                $table->dropColumn('warehouse_number');
            });
        }
    }
};
