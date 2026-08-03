<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_paths', function (Blueprint $table) {
            $table->id();

            $table->morphs('pathable');

            $table->string('type', 30)->index();
            $table->string('request_type', 30)->index();
            $table->string('status', 30)->default('matching')->index();

            $table->json('legs')->nullable();

            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('currency', 3)->default('EGP');

            $table->string('failure_reason', 255)->nullable();

            $table->timestamp('courier_confirmed_at')->nullable();
            $table->timestamp('submitted_to_client_at')->nullable();
            $table->timestamp('client_selected_at')->nullable();
            $table->timestamp('execution_started_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('metadata')->nullable();

            $table->timestamps();

            $table->index(['pathable_type', 'pathable_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_paths');
    }
};
