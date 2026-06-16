<?php

use App\Enum\OrderStatus;
use App\Models\VendorBranch;
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
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ecommerce_order_items', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('ecommerce_order_items', 'status')) {
                $table->string('status')->default(OrderStatus::PENDING->value)->after('total_price');
            }
            if (!Schema::hasColumn('ecommerce_order_items', 'pickup_branch_id')) {
                $table->foreignIdFor(VendorBranch::class, 'pickup_branch_id')->nullable()->after('status')->constrained('vendor_branches');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ecommerce_order_items', 'pickup_branch_id')) {
                $table->dropConstrainedForeignId('pickup_branch_id');
            }
            if (Schema::hasColumn('ecommerce_order_items', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('ecommerce_order_items', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
        });
    }
};


