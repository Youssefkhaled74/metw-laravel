<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Enum\BusinessProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompleteWarehouseBusinessProfileRequest;
use App\Http\Requests\Admin\RejectBusinessProfileRequest;
use App\Models\Warehouse;
use App\Services\CompleteWarehouseBusinessProfileService;
use Illuminate\Support\Facades\Auth;

class WarehouseBusinessProfileController extends Controller
{
    public function __construct(
        protected CompleteWarehouseBusinessProfileService $completeWarehouseBusinessProfileService
    ) {
    }

    public function upsert(CompleteWarehouseBusinessProfileRequest $request, Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.warehouses.update')) {
            return view('dashboard.admin.no-permission');
        }

        $this->completeWarehouseBusinessProfileService->complete(
            $warehouse,
            $request->validated(),
            $request
        );

        return redirect()->back()->with('success', 'Warehouse business profile submitted for review successfully.');
    }

    public function approve(Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.warehouses.update')) {
            return view('dashboard.admin.no-permission');
        }

        $profile = $warehouse->businessProfile()->firstOrFail();

        $profile->update([
            'status' => BusinessProfileStatus::APPROVED,
            'reviewed_at' => now(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Warehouse business profile approved successfully.');
    }

    public function reject(RejectBusinessProfileRequest $request, Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.warehouses.update')) {
            return view('dashboard.admin.no-permission');
        }

        $profile = $warehouse->businessProfile()->firstOrFail();

        $profile->update([
            'status' => BusinessProfileStatus::REJECTED,
            'reviewed_at' => now(),
            'approved_at' => null,
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Warehouse business profile rejected successfully.');
    }
}
