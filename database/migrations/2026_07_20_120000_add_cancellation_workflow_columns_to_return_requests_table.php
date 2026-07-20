<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('return_requests', 'seller_status')) {
                $table->string('seller_status')->nullable()->after('status');
            }
            if (! Schema::hasColumn('return_requests', 'seller_rejection_reason')) {
                $table->text('seller_rejection_reason')->nullable()->after('seller_status');
            }
            if (! Schema::hasColumn('return_requests', 'return_rejection_reason')) {
                $table->text('return_rejection_reason')->nullable()->after('seller_rejection_reason');
            }
            if (! Schema::hasColumn('return_requests', 'admin_reactivation_reason')) {
                $table->text('admin_reactivation_reason')->nullable()->after('return_rejection_reason');
            }
            if (! Schema::hasColumn('return_requests', 'reactivated_at')) {
                $table->timestamp('reactivated_at')->nullable()->after('admin_reactivation_reason');
            }
            if (! Schema::hasColumn('return_requests', 'inspected_at')) {
                $table->timestamp('inspected_at')->nullable()->after('reactivated_at');
            }
            if (! Schema::hasColumn('return_requests', 'admin_refund_amount')) {
                $table->decimal('admin_refund_amount', 12, 2)->nullable()->after('inspected_at');
            }
            if (! Schema::hasColumn('return_requests', 'wallet_credited_at')) {
                $table->timestamp('wallet_credited_at')->nullable()->after('admin_refund_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn([
                'seller_status',
                'seller_rejection_reason',
                'return_rejection_reason',
                'admin_reactivation_reason',
                'reactivated_at',
                'inspected_at',
                'admin_refund_amount',
                'wallet_credited_at',
            ]);
        });
    }
};
