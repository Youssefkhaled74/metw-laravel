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
        Schema::create('return_cash_backs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->enum('cash_back_method', ['lasco_wallet', 'insta_pay', 'mobile_wallet']);
            $table->decimal('value', 10, 2);
            $table->timestamps();

            $table->foreign('return_id')->references('id')->on('return_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_cash_backs');
    }
};
