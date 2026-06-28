<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_profiles', function (Blueprint $table) {
            $table->id();
            $table->morphs('profileable');
            $table->string('account_number')->nullable();
            $table->string('display_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('national_id', 100)->nullable();
            $table->string('preferred_locale', 10)->nullable();
            $table->text('bio')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('account_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_profiles');
    }
};
