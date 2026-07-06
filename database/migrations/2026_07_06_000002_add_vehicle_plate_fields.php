<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('representative_vehicles', function (Blueprint $table) {
            $table->string('registration_plate_letters', 50)->nullable()->after('transport_type_id');
            $table->string('registration_plate_numbers', 50)->nullable()->after('registration_plate_letters');
        });
    }

    public function down(): void
    {
        Schema::table('representative_vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'registration_plate_letters',
                'registration_plate_numbers',
            ]);
        });
    }
};
