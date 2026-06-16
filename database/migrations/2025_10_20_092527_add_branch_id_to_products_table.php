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
            if(!Schema::hasColumn('products', 'branch_id')) {
                $table->foreignIdFor(\App\Models\VendorBranch::class,'branch_id')->nullable()->after('vendor_id')->constrained('vendor_branches');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if(Schema::hasColumn('products', 'branch_id')) {
                $table->dropConstrainedForeignIdFor(\App\Models\VendorBranch::class,'branch_id');
            }
        });
    }
};
