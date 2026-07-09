<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            $table->foreignId('representative_id')
                ->nullable()
                ->constrained('representatives')
                ->nullOnDelete();
            $table->foreignId('rejection_reason_id')
                ->nullable()
                ->constrained('rejection_reasons')
                ->nullOnDelete();
            $table->text('rejection_note')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shipment_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rejection_reason_id');
            $table->dropConstrainedForeignId('representative_id');
            $table->dropColumn(['rejection_note', 'accepted_at', 'rejected_at']);
        });
    }
};
