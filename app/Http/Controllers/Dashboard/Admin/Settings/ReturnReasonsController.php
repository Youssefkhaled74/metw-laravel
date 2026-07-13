<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ReturnReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnReasonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.index')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $returnReasons = ReturnReason::withoutGlobalScope('active')->latest()->paginate(15);

        return view(
            'dashboard.admin.settings.return-reasons.index',
            compact('returnReasons')
        );
    }

    public function create()
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.create')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.return-reasons.create');
    }

    public function store(Request $request)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.store')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'reason_text' => 'required|string|max:255|unique:return_reasons,reason_text',
            'is_active'   => 'nullable|boolean',
        ]);

        ReturnReason::create([
            'reason_text' => $validated['reason_text'],
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.return-reasons.index')
            ->with('success', __('admin-dashboard.return-reasons.created_success'));
    }

    public function edit(ReturnReason $returnReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.edit')
        ) {
            return view('dashboard.admin.no-permission');
        }

        return view(
            'dashboard.admin.settings.return-reasons.edit',
            compact('returnReason')
        );
    }

    public function update(Request $request, ReturnReason $returnReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.update')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'reason_text' => 'required|string|max:255|unique:return_reasons,reason_text,' . $returnReason->id,
            'is_active'   => 'nullable|boolean',
        ]);

        $returnReason->update([
            'reason_text' => $validated['reason_text'],
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.return-reasons.index')
            ->with('success', __('admin-dashboard.return-reasons.updated_success'));
    }

    public function destroy(ReturnReason $returnReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.destroy')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $returnReason->delete();

        return redirect()
            ->route('admin.settings.return-reasons.index')
            ->with('success', __('admin-dashboard.return-reasons.deleted_success'));
    }

    public function toggleStatus(ReturnReason $returnReason)
    {
        if (
            Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.settings.return-reasons.toggle-status')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $returnReason->update([
            'is_active' => !$returnReason->is_active,
        ]);

        return redirect()
            ->route('admin.settings.return-reasons.index')
            ->with('success', __('admin-dashboard.return-reasons.status_updated'));
    }
}
