<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'user_number')) {
                $table->string('user_number')->nullable()->unique('users_user_number_unique')->after('id');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'vendor_number')) {
                $table->string('vendor_number')->nullable()->unique('vendors_vendor_number_unique')->after('id');
            }
        });

        Schema::table('shipment_companies', function (Blueprint $table) {
            if (! Schema::hasColumn('shipment_companies', 'company_number')) {
                $table->string('company_number')->nullable()->unique('shipment_companies_company_number_unique')->after('id');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'employee_number')) {
                $table->string('employee_number')->nullable()->unique('employees_employee_number_unique')->after('id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'product_number')) {
                $table->string('product_number')->nullable()->unique('products_product_number_unique')->after('id');
            }
        });

        $this->backfillExistingNumbers();
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_number')) {
                $table->dropUnique('users_user_number_unique');
                $table->dropColumn('user_number');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'vendor_number')) {
                $table->dropUnique('vendors_vendor_number_unique');
                $table->dropColumn('vendor_number');
            }
        });

        Schema::table('shipment_companies', function (Blueprint $table) {
            if (Schema::hasColumn('shipment_companies', 'company_number')) {
                $table->dropUnique('shipment_companies_company_number_unique');
                $table->dropColumn('company_number');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'employee_number')) {
                $table->dropUnique('employees_employee_number_unique');
                $table->dropColumn('employee_number');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_number')) {
                $table->dropUnique('products_product_number_unique');
                $table->dropColumn('product_number');
            }
        });
    }

    private function backfillExistingNumbers(): void
    {
        $this->fillNumberColumn('users', 'user_number', 'USR');
        $this->fillNumberColumn('vendors', 'vendor_number', 'VDR');
        $this->fillNumberColumn('shipment_companies', 'company_number', 'SHC');
        $this->fillNumberColumn('employees', 'employee_number', 'EMP');
        $this->fillNumberColumn('products', 'product_number', 'PRD');
    }

    private function fillNumberColumn(string $table, string $column, string $prefix): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::table($table)
            ->whereNull($column)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $column, $prefix) {
                foreach ($rows as $row) {
                    $number = $this->generateUniqueNumber($table, $column, $prefix);

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update([$column => $number]);
                }
            }, 'id');
    }

    private function generateUniqueNumber(string $table, string $column, string $prefix): string
    {
        do {
            $token = strtoupper(bin2hex(random_bytes(4)));
            $number = $prefix . '-' . $token;
        } while (DB::table($table)->where($column, $number)->exists());

        return $number;
    }
};
