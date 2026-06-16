<?php

use App\Enum\AddressType;
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
        Schema::create('package_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->text('landmark')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('type');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_addresses');
    }
};
