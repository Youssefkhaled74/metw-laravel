<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectionReasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.index')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $rejectionReasons = RejectionReason::withoutGlobalScope('active')->latest()->paginate(10);

        return view(
            'dashboard.admin.settings.rejection-reasons.index',
            compact('rejectionReasons')
        );
    }

    public function create()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.create')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.rejection-reasons.create');
    }

    public function store(Request $request)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.store')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'reason_text' => 'required|string|max:255|unique:rejection_reasons,reason_text',
            'is_active'   => 'nullable|boolean',
        ]);

        RejectionReason::create([
            'reason_text' => $validated['reason_text'],
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.rejection-reasons.index')
            ->with('success', 'Rejection reason created successfully.');
    }

    public function edit(RejectionReason $rejectionReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.edit')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view(
            'dashboard.admin.settings.rejection-reasons.edit',
            compact('rejectionReason')
        );
    }

    public function update(Request $request, RejectionReason $rejectionReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.update')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'reason_text' => 'required|string|max:255|unique:rejection_reasons,reason_text,' . $rejectionReason->id,
            'is_active'   => 'nullable|boolean',
        ]);

        $rejectionReason->update([
            'reason_text' => $validated['reason_text'],
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.rejection-reasons.index')
            ->with('success', 'Rejection reason updated successfully.');
    }

    public function destroy(RejectionReason $rejectionReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.destroy')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $rejectionReason->delete();

        return redirect()
            ->route('admin.settings.rejection-reasons.index')
            ->with('success', 'Rejection reason deleted successfully.');
    }

    public function toggleStatus(RejectionReason $rejectionReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.rejection-reasons.toggle-status')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $rejectionReason->update([
            'is_active' => !$rejectionReason->is_active,
        ]);

        return redirect()
            ->route('admin.settings.rejection-reasons.index')
            ->with('success', 'Rejection reason status updated successfully.');
    }
}
