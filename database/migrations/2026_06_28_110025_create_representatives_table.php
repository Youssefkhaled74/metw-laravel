<?php

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Warehouse::class)->nullable()->constrained()->nullOnDelete();
            $table->string('account_type', 30)->default(RepresentativeAccountType::FREE->value);
            $table->string('status', 30)->default(RepresentativeStatus::INCOMPLETE->value);
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['warehouse_id', 'status']);
            $table->index(['account_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
