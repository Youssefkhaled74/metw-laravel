,<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * Run the migrations.
         */
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('username');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable()->unique();
            $table->string('country_code',10);

            $table->string('password');
            $table->rememberToken();

            $table->timestamp('last_login_at')->nullable();

            $table->string('photo', 2048)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestampsTz();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
