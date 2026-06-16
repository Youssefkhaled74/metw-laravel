<?php

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
        Schema::create('vendor_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(App\Models\Vendor::class)->constrained();
            $table->foreignIdFor(App\Models\State::class)->constrained();
            $table->foreignIdFor(App\Models\City::class)->constrained();
            $table->foreignIdFor(App\Models\Zone::class)->constrained();
            $table->string('street_main')->nullable();
            $table->string('street_sub')->nullable();
            $table->integer('building')->nullable();
            $table->string('building_name')->nullable();
            $table->integer('floor')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('status')->default(true);   
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_branches');
    }
};
