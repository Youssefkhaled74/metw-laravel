<?php

use App\Models\Governorate;
use App\Models\Representative;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_service_governorates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Representative::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Governorate::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['representative_id', 'governorate_id'], 'rep_service_governorates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_service_governorates');
    }
};
