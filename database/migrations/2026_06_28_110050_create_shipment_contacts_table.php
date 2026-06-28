<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('contact_number', 100);
            $table->string('full_name');
            $table->string('primary_mobile', 30);
            $table->string('secondary_mobile', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'type']);
            $table->unique(['user_id', 'contact_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_contacts');
    }
};
