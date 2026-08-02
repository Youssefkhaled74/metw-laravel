<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_assignments', function (Blueprint $table) {
            $table->id();

            $table->morphs('assignable');

            $table->string('leg_type', 50);
            $table->foreignId('representative_id')
                ->nullable()
                ->constrained('representatives')
                ->nullOnDelete();

            $table->string('status', 30)->default('pending')->index();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('offered_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamp('response_deadline_at')->nullable();
            $table->timestamp('window_opens_at')->nullable();
            $table->timestamp('window_closes_at')->nullable();

            $table->unsignedInteger('auto_reject_working_hours')->default(7);
            $table->unsignedInteger('response_window_working_hours')->default(3);
            $table->string('auto_action', 50)->default('auto_assign_next');
            $table->timestamp('auto_action_executed_at')->nullable();

            $table->foreignId('rejection_reason_id')->nullable()->constrained('rejection_reasons')->nullOnDelete();
            $table->string('rejection_note')->nullable();

            $table->text('metadata')->nullable();
            $table->timestamps();

            $table->index(['assignable_type', 'assignable_id', 'status']);
            $table->index(['representative_id', 'status']);
            $table->index('leg_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_assignments');
    }
};
