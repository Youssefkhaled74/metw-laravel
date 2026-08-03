<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('shipment_requests', 'request_type')) {
                $table->string('request_type', 30)->nullable()->after('shipping_type');
            }

            if (! Schema::hasColumn('shipment_requests', 'is_fast_delivery')) {
                $table->boolean('is_fast_delivery')->default(false)->after('request_type');
            }

            if (! Schema::hasColumn('shipment_requests', 'submitter_type')) {
                $table->string('submitter_type', 20)->default('user')->after('user_id');
            }

            if (! Schema::hasColumn('shipment_requests', 'submitter_id')) {
                $table->unsignedBigInteger('submitter_id')->nullable()->after('submitter_type');
                $table->index(['submitter_type', 'submitter_id']);
            }

            if (! Schema::hasColumn('shipment_requests', 'selected_request_path_id')) {
                $table->foreignId('selected_request_path_id')
                    ->nullable()
                    ->after('representative_id')
                    ->constrained('request_paths')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('shipment_requests', 'failure_text')) {
                $table->text('failure_text')->nullable()->after('rejection_note');
            }

            if (! Schema::hasColumn('shipment_requests', 'paths_evaluated_at')) {
                $table->timestamp('paths_evaluated_at')->nullable()->after('failure_text');
            }

            if (! Schema::hasColumn('shipment_requests', 'execution_started_at')) {
                $table->timestamp('execution_started_at')->nullable()->after('paths_evaluated_at');
            }

            if (! Schema::hasColumn('shipment_requests', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('execution_started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            $table->dropForeign(['selected_request_path_id']);
            $table->dropColumn([
                'request_type',
                'is_fast_delivery',
                'submitter_type',
                'submitter_id',
                'selected_request_path_id',
                'failure_text',
                'paths_evaluated_at',
                'execution_started_at',
                'closed_at',
            ]);
        });
    }
};
