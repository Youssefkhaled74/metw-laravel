<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();

            $table->morphs('payable');

            $table->foreignId('request_path_id')
                ->nullable()
                ->constrained('request_paths')
                ->nullOnDelete();

            $table->string('submitter_type', 20)->default('user');
            $table->unsignedBigInteger('submitter_id')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status', 20)->default('pending')->index();

            $table->string('payment_method', 30)->nullable();
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->text('metadata')->nullable();

            $table->timestamps();

            $table->index(['payable_type', 'payable_id', 'status']);
            $table->index('submitter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
