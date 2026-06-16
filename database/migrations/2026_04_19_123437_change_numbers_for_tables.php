<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->resetAndRefill('users', 'user_number', 'USR');
        $this->resetAndRefill('vendors', 'vendor_number', 'VDR');
        $this->resetAndRefill('shipment_companies', 'company_number', 'SHC');
        $this->resetAndRefill('employees', 'employee_number', 'EMP');
        $this->resetAndRefill('products', 'product_number', 'PRD');
    }

    public function down(): void
    {
        $this->clearColumn('users', 'user_number');
        $this->clearColumn('vendors', 'vendor_number');
        $this->clearColumn('shipment_companies', 'company_number');
        $this->clearColumn('employees', 'employee_number');
        $this->clearColumn('products', 'product_number');
    }

    private function resetAndRefill(string $table, string $column, string $prefix): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        // 1. مسح القيم القديمة
        DB::table($table)->update([$column => null]);

        // 2. إعادة التوليد
        $counter = 1;

        DB::table($table)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $column, $prefix, &$counter) {
                foreach ($rows as $row) {
                    $number = $this->generateSequentialNumber($prefix, $counter);

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update([$column => $number]);

                    $counter++;
                }
            }, 'id');
    }

    private function clearColumn(string $table, string $column): void
    {
        if (Schema::hasColumn($table, $column)) {
            DB::table($table)->update([$column => null]);
        }
    }

    private function generateSequentialNumber(string $prefix, int $counter): string
    {
        return $prefix . '-' . str_pad($counter, 8, '0', STR_PAD_LEFT);
    }
};
