<?php

use App\Enum\BusinessProfileStatus;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vendor::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('legal_name');
            $table->string('commercial_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('commercial_register_number')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('status', 30)->default(BusinessProfileStatus::INCOMPLETE->value);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_business_profiles');
    }
};
