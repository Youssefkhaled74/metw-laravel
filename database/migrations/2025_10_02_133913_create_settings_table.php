<?php
// database/migrations/2025_10_02_create_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // insert default min/max (اختياري)
        DB::table('settings')->insert([
            ['key' => 'price_per_km_min', 'value' => '0.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'price_per_km_max', 'value' => '100.00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
