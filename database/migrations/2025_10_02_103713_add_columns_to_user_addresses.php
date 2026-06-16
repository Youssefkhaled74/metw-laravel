<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Zone;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('user_addresses', 'country_id')) {
                $table->foreignIdFor(Country::class)->nullable()->after('user_id')->constrained();
            }
            if (!Schema::hasColumn('user_addresses', 'state_id')) {
                $table->foreignIdFor(State::class)->nullable()->after('country_id')->constrained();
            }
            if (!Schema::hasColumn('user_addresses', 'zone_id')) {
                $table->foreignIdFor(Zone::class)->nullable()->after('city_id')->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Country::class);
            $table->dropConstrainedForeignIdFor(State::class);
            $table->dropConstrainedForeignIdF(Zone::class);
        });
    }
};
