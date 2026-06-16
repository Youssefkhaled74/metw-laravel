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
        Schema::table('package_addresses', function (Blueprint $table) {
            if(Schema::hasColumn('package_addresses','location')){
                $table->string('location')->nullable()->change();
            }
            if(Schema::hasColumn('package_addresses', 'country')){
                $table->string('country')->nullable()->change();
            }
            if(Schema::hasColumn('package_addresses', 'city')){
                $table->string('city')->nullable()->change();
            }
            if(Schema::hasColumn('package_addresses', 'state')){
                $table->string('state')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_addresses', function (Blueprint $table) {
            if(Schema::hasColumn('package_addresses','location')){
                $table->string('location')->change();
            }
            if(Schema::hasColumn('package_addresses', 'country')){
                $table->string('country')->change();
            }
            if(Schema::hasColumn('package_addresses', 'city')){
                $table->string('city')->change();
            }
            if(Schema::hasColumn('package_addresses', 'state')){
                $table->string('state')->change();
            }
        });
    }
};
