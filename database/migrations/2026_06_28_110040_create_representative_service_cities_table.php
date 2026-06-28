<?php

use App\Models\City;
use App\Models\Representative;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_service_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Representative::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['representative_id', 'city_id'], 'rep_service_cities_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_service_cities');
    }
};
