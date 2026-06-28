<?php

use App\Models\Representative;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_work_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Representative::class)->constrained()->cascadeOnDelete();
            $table->string('work_type', 50);
            $table->timestamps();

            $table->unique(['representative_id', 'work_type'], 'rep_work_types_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_work_types');
    }
};
