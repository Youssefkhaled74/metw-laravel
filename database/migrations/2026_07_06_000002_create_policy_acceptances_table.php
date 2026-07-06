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
        Schema::create('policy_acceptances', function (Blueprint $table) {
            $table->id();
            $table->morphs('accountable');
            $table->string('account_type');
            $table->string('policy_version')->nullable();
            $table->timestamp('accepted_at');
            $table->timestamps();

            $table->unique(['accountable_type', 'accountable_id', 'account_type'], 'policy_acceptances_account_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_acceptances');
    }
};
