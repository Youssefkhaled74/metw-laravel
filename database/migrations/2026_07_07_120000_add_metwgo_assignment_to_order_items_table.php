<?php

use App\Models\Representative;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'representative_id')) {
                $table->foreignIdFor(Representative::class)
                    ->nullable()
                    ->after('shipment_company_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('order_items', 'accepted_fee')) {
                $table->decimal('accepted_fee', 10, 2)
                    ->nullable()
                    ->after('est_price');
            }

            if (! Schema::hasColumn('order_items', 'accepted_at')) {
                $table->timestamp('accepted_at')
                    ->nullable()
                    ->after('accepted_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'accepted_at')) {
                $table->dropColumn('accepted_at');
            }

            if (Schema::hasColumn('order_items', 'accepted_fee')) {
                $table->dropColumn('accepted_fee');
            }

            if (Schema::hasColumn('order_items', 'representative_id')) {
                $table->dropConstrainedForeignId('representative_id');
            }
        });
    }
};
