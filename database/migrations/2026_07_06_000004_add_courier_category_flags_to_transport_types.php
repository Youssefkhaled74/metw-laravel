<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_types', function (Blueprint $table) {
            $table->boolean('category_1_available')->default(false)->after('max_volume');
            $table->boolean('category_2_available')->default(false)->after('category_1_available');
            $table->boolean('category_3_available')->default(false)->after('category_2_available');
            $table->boolean('requires_driving_license')->default(false)->after('category_3_available');
            $table->boolean('requires_vehicle_license')->default(false)->after('requires_driving_license');
        });
    }

    public function down(): void
    {
        Schema::table('transport_types', function (Blueprint $table) {
            $table->dropColumn([
                'category_1_available',
                'category_2_available',
                'category_3_available',
                'requires_driving_license',
                'requires_vehicle_license',
            ]);
        });
    }
};
