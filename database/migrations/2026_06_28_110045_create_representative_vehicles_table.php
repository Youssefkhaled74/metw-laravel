<?php

use App\Models\Representative;
use App\Models\TransportType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Representative::class)->unique()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TransportType::class)->nullable()->constrained()->nullOnDelete();
            $table->string('registration_number')->nullable()->unique();
            $table->string('license_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->unsignedSmallInteger('manufacture_year')->nullable();
            $table->decimal('max_weight', 10, 2)->nullable();
            $table->decimal('max_volume', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_vehicles');
    }
};
