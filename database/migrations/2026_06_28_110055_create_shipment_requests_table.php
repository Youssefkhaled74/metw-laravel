<?php

use App\Enum\ShipmentRequestStatus;
use App\Models\ShipmentContact;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('request_number')->unique();
            $table->foreignIdFor(ShipmentContact::class, 'sender_contact_id')->constrained('shipment_contacts')->restrictOnDelete();
            $table->foreignIdFor(ShipmentContact::class, 'receiver_contact_id')->constrained('shipment_contacts')->restrictOnDelete();
            $table->string('status', 20)->default(ShipmentRequestStatus::DRAFT->value);
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_requests');
    }
};
