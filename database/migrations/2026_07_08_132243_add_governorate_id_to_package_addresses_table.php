<?php

use App\Models\Governorate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('package_addresses', 'governorate_id')) {
                $table->foreignIdFor(Governorate::class)->nullable()->after('country_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('package_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('package_addresses', 'governorate_id')) {
                $table->dropForeign(['governorate_id']);
                $table->dropColumn('governorate_id');
            }
        });
    }
};
