<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile_primary')) {
                $table->string('mobile_primary')->unique()->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'mobile_secondary')) {
                $table->string('mobile_secondary')->unique()->nullable()->after('mobile_primary');
            }

            if (!Schema::hasColumn('users', 'mobile_primary_verified_at')) {
                $table->timestamp('mobile_primary_verified_at')->nullable()->after('mobile_secondary');
            }

            if (!Schema::hasColumn('users', 'mobile_secondary_verified_at')) {
                $table->timestamp('mobile_secondary_verified_at')->nullable()->after('mobile_primary_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mobile_primary')) {
                $table->dropColumn('mobile_primary');
            }

            if (Schema::hasColumn('users', 'mobile_secondary')) {
                $table->dropColumn('mobile_secondary');
            }

            if (Schema::hasColumn('users', 'mobile_primary_verified_at')) {
                $table->dropColumn('mobile_primary_verified_at');
            }

            if (Schema::hasColumn('users', 'mobile_secondary_verified_at')) {
                $table->dropColumn('mobile_secondary_verified_at');
            }
        });
    }
};
