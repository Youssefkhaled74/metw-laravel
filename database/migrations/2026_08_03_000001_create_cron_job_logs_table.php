<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_job_logs', function (Blueprint $table) {
            $table->id();

            $table->string('job_name', 100)->index();
            $table->string('status', 20)->default('running')->index();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->float('duration')->nullable()->comment('seconds');

            $table->unsignedInteger('records_processed')->default(0);
            $table->unsignedInteger('records_affected')->default(0);

            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['job_name', 'status']);
            $table->index(['job_name', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_job_logs');
    }
};
