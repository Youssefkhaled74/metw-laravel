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
        Schema::table('cart_items', function (Blueprint $table) {
            // نتأكد إن العمود مش موجود قبل الإضافة
            if (!Schema::hasColumn('cart_items', 'requires_split')) {
                $table->boolean('requires_split')->default(false)->after('est_price'); // استبدل some_column بالعمود اللي بعده
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'requires_split')) {
                $table->dropColumn('requires_split');
            }
        });
    }
};
