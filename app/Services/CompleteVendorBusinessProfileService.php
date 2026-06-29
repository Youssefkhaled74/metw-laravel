<?php

namespace App\Services;

use App\Enum\BusinessProfileStatus;
use App\Models\Vendor;
use App\Models\VendorBusinessProfile;
use App\Services\Concerns\HandlesBusinessProfileDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompleteVendorBusinessProfileService
{
    use HandlesBusinessProfileDocuments;

    public function complete(Vendor $vendor, array $data, Request $request): VendorBusinessProfile
    {
        return DB::transaction(function () use ($vendor, $data, $request) {
            $profile = $vendor->businessProfile()->firstOrNew();

            $profile->fill([
                'legal_name' => $data['legal_name'],
                'commercial_name' => $data['commercial_name'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'commercial_register_number' => $data['commercial_register_number'] ?? null,
                'contact_name' => $data['contact_name'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => BusinessProfileStatus::PENDING_REVIEW,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'approved_at' => null,
                'rejection_reason' => null,
            ]);

            $profile->vendor()->associate($vendor);
            $profile->save();

            $this->syncDocuments($profile, $request, 'vendor-business-profiles');

            return $profile->fresh('mediaFiles');
        });
    }
}
