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
        Schema::table('shipment_companies', function (Blueprint $table) {
            if (!Schema::hasColumn('shipment_companies', 'facebook_url')) {
                $table->string('facebook_url')->nullable()->after('logo');
            }
            if(!Schema::hasColumn('shipment_companies', 'whatsapp_url')){
                $table->string('whatsapp_url')->nullable()->after('facebook_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_companies', function (Blueprint $table) {
            $table->dropColumn('facebook_url');
            $table->dropColumn('whatsapp_url');
        });
    }
};
