<?php

namespace App\Services;

use App\Enum\BusinessProfileStatus;
use App\Models\Warehouse;
use App\Models\WarehouseBusinessProfile;
use App\Services\Concerns\HandlesBusinessProfileDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompleteWarehouseBusinessProfileService
{
    use HandlesBusinessProfileDocuments;

    public function complete(Warehouse $warehouse, array $data, Request $request): WarehouseBusinessProfile
    {
        return DB::transaction(function () use ($warehouse, $data, $request) {
            $profile = $warehouse->businessProfile()->firstOrNew();

            $profile->fill([
                'legal_name' => $data['legal_name'],
                'commercial_name' => $data['commercial_name'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'commercial_register_number' => $data['commercial_register_number'] ?? null,
                'manager_name' => $data['manager_name'] ?? null,
                'manager_phone' => $data['manager_phone'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'status' => BusinessProfileStatus::PENDING_REVIEW,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'approved_at' => null,
                'rejection_reason' => null,
            ]);

            $profile->warehouse()->associate($warehouse);
            $profile->save();

            $this->syncDocuments($profile, $request, 'warehouse-business-profiles');

            return $profile->fresh('mediaFiles');
        });
    }
}
