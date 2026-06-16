<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_split')->default(false)->after('est_price');
            $table->foreignId('parent_id')->nullable()->after('is_split')
                ->constrained('order_items')->onDelete('cascade');

            // Add index for better query performance
            $table->index('parent_id');
            $table->index(['order_id', 'is_split']);
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['order_id', 'is_split']);
            $table->dropColumn(['is_split', 'parent_id']);
        });
    }
};
