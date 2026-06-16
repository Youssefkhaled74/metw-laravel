<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.index')) {
            return view('dashboard.admin.no-permission');
        }

        $allowedSortColumns = ['id', 'name_ar', 'name_en', 'cities_count', 'created_at'];
        $sortBy = $request->string('sort_by', 'created_at')->toString();
        $sortDir = strtolower($request->string('sort_dir', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $statesQuery = State::withoutGlobalScope('active')
            ->with('country')
            ->withCount('cities');

        if ($sortBy === 'cities_count') {
            $statesQuery->orderBy('cities_count', $sortDir);
        } else {
            $statesQuery->orderBy($sortBy, $sortDir);
        }

        $states = $statesQuery->paginate(15)->appends($request->query());

        return view('dashboard.admin.settings.states.index', compact('states', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.states.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.store')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['country_id'] = $this->getEgyptCountryId();

        State::create($data);
        return redirect()->route('admin.settings.states.index')->with('success', __('admin-dashboard.state_created_success'));
    }

    public function edit(State $state)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.states.edit', compact('state'));
    }

    public function update(Request $request, State $state)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.update')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['country_id'] = $this->getEgyptCountryId();

        $state->update($data);
        return redirect()->route('admin.settings.states.index')->with('success', __('admin-dashboard.state_updated_success'));
    }

    private function getEgyptCountryId(): int
    {
        return Country::firstOrCreate(
            ['name_en' => 'Egypt'],
            ['name_ar' => 'مصر', 'is_active' => 1]
        )->id;
    }

    public function destroy(State $state)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $state->delete();
        return redirect()->route('admin.settings.states.index')->with('success', __('admin-dashboard.state_deleted_success'));
    }

    // 🔥 توجل (تفعيل/إلغاء)
    public function toggle(State $state)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.states.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $state->update(['is_active' => !$state->is_active]);

        return redirect()
            ->route('admin.settings.states.index')
            ->with('success', __('admin-dashboard.state_status_updated_success'));
    }
}
