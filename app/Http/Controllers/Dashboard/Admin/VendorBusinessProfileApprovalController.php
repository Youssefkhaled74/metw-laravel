<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\BusinessProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectBusinessProfileRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class VendorBusinessProfileApprovalController extends Controller
{
    public function approve(int $vendorId)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.vendors.update')) {
            return view('dashboard.admin.no-permission');
        }

        $vendor = Vendor::withoutGlobalScope('active')->findOrFail($vendorId);
        $profile = $vendor->businessProfile()->firstOrFail();

        $profile->update([
            'status' => BusinessProfileStatus::APPROVED,
            'reviewed_at' => now(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Vendor business profile approved successfully.');
    }

    public function reject(RejectBusinessProfileRequest $request, int $vendorId)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.vendors.update')) {
            return view('dashboard.admin.no-permission');
        }

        $vendor = Vendor::withoutGlobalScope('active')->findOrFail($vendorId);
        $profile = $vendor->businessProfile()->firstOrFail();

        $profile->update([
            'status' => BusinessProfileStatus::REJECTED,
            'reviewed_at' => now(),
            'approved_at' => null,
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Vendor business profile rejected successfully.');
    }
}
