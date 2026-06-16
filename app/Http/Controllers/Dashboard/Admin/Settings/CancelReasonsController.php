<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancelReasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.index')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $cancelReasons = CancelReason::withoutGlobalScope('active')->latest()->paginate(10);

        return view(
            'dashboard.admin.settings.cancel-reasons.index',
            compact('cancelReasons')
        );
    }

    public function create()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.create')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.cancel-reasons.create');
    }

    public function store(Request $request)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.store')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'name_en'  => 'required|string|max:255|unique:cancel_reasons,name_en',
            'name_ar'  => 'required|string|max:255|unique:cancel_reasons,name_ar',
            'is_active'=> 'nullable|boolean',
        ]);

        CancelReason::create([
            'name_en'   => $validated['name_en'],
            'name_ar'   => $validated['name_ar'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.cancel-reasons.index')
            ->with('success', 'Cancel reason created successfully.');
    }

    public function edit(CancelReason $cancelReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.edit')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view(
            'dashboard.admin.settings.cancel-reasons.edit',
            compact('cancelReason')
        );
    }

    public function update(Request $request, CancelReason $cancelReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.update')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255|unique:cancel_reasons,name_en,' . $cancelReason->id,
            'name_ar' => 'required|string|max:255|unique:cancel_reasons,name_ar,' . $cancelReason->id,
            'is_active' => 'nullable|boolean',
        ]);

        $cancelReason->update([
            'name_en'   => $validated['name_en'],
            'name_ar'   => $validated['name_ar'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.cancel-reasons.index')
            ->with('success', 'Cancel reason updated successfully.');
    }

    public function destroy(CancelReason $cancelReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.destroy')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $cancelReason->delete();

        return redirect()
            ->route('admin.settings.cancel-reasons.index')
            ->with('success', 'Cancel reason deleted successfully.');
    }

    public function toggleStatus(CancelReason $cancelReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.cancel-reasons.toggle-status')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $cancelReason->update([
            'is_active' => ! $cancelReason->is_active
        ]);

        return redirect()
            ->route('admin.settings.cancel-reasons.index')
            ->with('success', 'Cancel reason status updated successfully.');
    }
}
