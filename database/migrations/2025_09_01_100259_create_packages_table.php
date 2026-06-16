<?php

use App\Models\ConsignmentType;
use App\Models\DeliveryType;
use App\Models\PackageAddress;
use App\Models\PackageDetails;
use App\Models\Size;
use App\Models\PackageType;
use App\Models\ShipmentCompany;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_number')->unique();
            $table->foreignIdFor(PackageType::class , 'type_id')->constrained('package_types');
            $table->foreignIdFor(Size::class,'size_id')->constrained('sizes');
            $table->foreignIdFor(PackageAddress::class , 'pickup_address_id')->constrained('package_addresses')->cascadeOnDelete();
            $table->foreignIdFor(PackageAddress::class , 'dropoff_address_id')->constrained('package_addresses')->cascadeOnDelete();
            $table->foreignIdFor(PackageDetails::class , 'package_details_id')->constrained('package_details')->cascadeOnDelete();
            $table->foreignIdFor(ShipmentCompany::class,'shipment_company_id')->constrained('shipment_companies')->cascadeOnDelete();
            $table->foreignIdFor(DeliveryType::class)->constrained();
            $table->foreignIdFor(ConsignmentType::class)->constrained();
            $table->text('note')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
