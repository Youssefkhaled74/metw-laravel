<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('phone')->nullable();

            $table->foreignIdFor(Country::class)->nullable()->constrained('countries');
            $table->foreignIdFor(State::class)->nullable()->constrained('states');
            $table->foreignIdFor(City::class)->nullable()->constrained('cities');
            $table->foreignIdFor(Zone::class)->nullable()->constrained('zones');
            $table->string('street_name')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('landmark')->nullable();
            $table->string('address_type')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // بيانات إضافية للستور نفسه

            $table->boolean('is_main')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
