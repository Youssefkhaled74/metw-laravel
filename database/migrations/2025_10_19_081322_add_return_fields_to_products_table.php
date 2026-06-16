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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_returnable')) {
                $table->boolean('is_returnable')->default(false)->after('price');
            }

            if (!Schema::hasColumn('products', 'return_fee')) {
                $table->decimal('return_fee', 10, 2)->nullable()->after('is_returnable');
            }
            if (!Schema::hasColumn('products', 'return_validity')) {
                $table->integer('return_validity')->nullable()->after('is_returnable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_returnable')) {
                $table->dropColumn('is_returnable');
            }

            if (Schema::hasColumn('products', 'return_fee')) {
                $table->dropColumn('return_fee');
            }
            if (Schema::hasColumn('products', 'return_validity')) {
                $table->dropColumn('return_validity');
            }
        });
    }
};
