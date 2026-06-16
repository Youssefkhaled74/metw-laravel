<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {

            // If size_id has a foreign key, drop it first
            if (Schema::hasColumn('packages', 'size_id')) {
                $table->dropForeign(['size_id']);
                $table->dropColumn('size_id');
            }

            // New numeric columns
            $table->unsignedInteger('size')->nullable()->after('type_id');
            $table->unsignedInteger('piece')->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {

            // Remove new columns
            $table->dropColumn(['size', 'piece']);

            // Restore size_id
            $table->unsignedBigInteger('size_id')->nullable()->after('type_id');

            // Optional: restore foreign key if needed
            // $table->foreign('size_id')->references('id')->on('sizes');
        });
    }
};
