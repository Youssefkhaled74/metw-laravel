<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'governorate_id')) {
                $table->foreignId('governorate_id')->nullable()->constrained('governorates')->cascadeOnDelete()->after('id');
            }
            if (!Schema::hasColumn('cities', 'excel_sort')) {
                $table->unsignedSmallInteger('excel_sort')->nullable()->index()->after('governorate_id');
            }
            if (!Schema::hasColumn('cities', 'is_capital')) {
                $table->boolean('is_capital')->default(false)->after('excel_sort');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'is_capital')) {
                $table->dropColumn('is_capital');
            }
            if (Schema::hasColumn('cities', 'excel_sort')) {
                $table->dropColumn('excel_sort');
            }
            if (Schema::hasColumn('cities', 'governorate_id')) {
                $table->dropConstrainedForeignId('governorate_id');
            }
        });
    }
};
