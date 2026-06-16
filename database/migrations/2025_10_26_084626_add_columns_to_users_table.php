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
        Schema::table('users', function (Blueprint $table) {
            // check if column doesn't exist before adding it
            if (!Schema::hasColumn('users', 'fcm_token_shipment')) {
                $table->string('fcm_token_shipment')->nullable()->after('notifications_enabled');
            }

            if (!Schema::hasColumn('users', 'enable_shipment_notifications')) {
                $table->boolean('enable_shipment_notifications')->default(true)->after('fcm_token_shipment');
            }

            if (!Schema::hasColumn('users', 'default_shipment_lang')) {
                $table->string('default_shipment_lang')->default('ar')->after('enable_shipment_notifications');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'fcm_token_shipment')) {
                $table->dropColumn('fcm_token_shipment');
            }

            if (Schema::hasColumn('users', 'enable_shipment_notifications')) {
                $table->dropColumn('enable_shipment_notifications');
            }

            if (Schema::hasColumn('users', 'default_shipment_lang')) {
                $table->dropColumn('default_shipment_lang');
            }
        });
    }
};
