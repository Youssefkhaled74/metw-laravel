<?php

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('package_addresses', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('package_addresses', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('package_addresses', 'country')) {
                $table->dropColumn('country');
            }

            if (!Schema::hasColumn('package_addresses', 'country_id')) {
                $table->foreignIdFor(Country::class)->nullable()->after('location')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('package_addresses', 'state_id')) {
                $table->foreignIdFor(State::class)->nullable()->after('country_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('package_addresses', 'city_id')) {
                $table->foreignIdFor(City::class)->nullable()->after('state_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('package_addresses', 'zone_id')) {
                $table->foreignIdFor(Zone::class)->nullable()->after('city_id')->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_addresses', function (Blueprint $table) {
            // احذف العلاقات والأعمدة الجديدة
            if (Schema::hasColumn('package_addresses', 'zone_id')) {
                $table->dropForeign(['zone_id']);
                $table->dropColumn('zone_id');
            }

            if (Schema::hasColumn('package_addresses', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }

            if (Schema::hasColumn('package_addresses', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }

            if (Schema::hasColumn('package_addresses', 'country_id')) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            }

            // أعد الأعمدة النصية القديمة
            if (!Schema::hasColumn('package_addresses', 'country')) {
                $table->string('country')->nullable()->after('location');
            }
            if (!Schema::hasColumn('package_addresses', 'state')) {
                $table->string('state')->nullable()->after('country');
            }
            if (!Schema::hasColumn('package_addresses', 'city')) {
                $table->string('city')->nullable()->after('state');
            }
        });
    }
};
