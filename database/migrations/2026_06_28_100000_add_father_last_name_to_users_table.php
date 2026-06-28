<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'father_name')) {
                $table->string('father_name')->nullable()->after('username');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('father_name');
            }

            if (!Schema::hasColumn('users', 'account_number')) {
                $table->string('account_number')->unique()->nullable()->after('image');
            }

            if (!Schema::hasColumn('users', 'account_opening_date')) {
                $table->timestamp('account_opening_date')->nullable()->after('account_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'father_name')) {
                $table->dropColumn('father_name');
            }

            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }

            if (Schema::hasColumn('users', 'account_number')) {
                $table->dropColumn('account_number');
            }

            if (Schema::hasColumn('users', 'account_opening_date')) {
                $table->dropColumn('account_opening_date');
            }
        });
    }
};
