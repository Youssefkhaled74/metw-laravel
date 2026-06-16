<?php

use App\Models\MainCategory;
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
            if (!Schema::hasColumn('products', 'main_category_id')) {
                $table->foreignIdFor(MainCategory::class)->after('vendor_id')->nullable()->constrained('main_categories');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'main_category_id')) {
                $table->dropConstrainedForeignIdFor(MainCategory::class);
            }
        });
    }
};
