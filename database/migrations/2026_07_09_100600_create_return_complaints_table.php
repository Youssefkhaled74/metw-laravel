<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_request_id');
            $table->foreign('return_request_id')->references('id')->on('order_return_requests')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->text('complaint_reason');
            $table->string('admin_action', 30)->nullable();
            $table->text('action_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_complaints');
    }
};
