<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:id,key,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $configsQuery = Config::query();

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $configsQuery->where(function ($query) use ($search) {
                $query->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        $configs = $configsQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->appends(request()->query());

        return view('dashboard.admin.settings.configs.index', compact('configs'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.create')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.configs.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'key'       => 'required|string|max:255|unique:configs,key',
            'value'     => 'nullable|string',
            'group'     => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Config::create($data);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config created successfully.');
    }

    public function edit(Config $config)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.edit')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.configs.edit', compact('config'));
    }

    public function update(Request $request, Config $config)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'key'       => 'required|string|max:255|unique:configs,key,' . $config->id,
            'value'     => 'nullable|string',
            'group'     => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $config->update($data);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config updated successfully.');
    }

    public function destroy(Config $config)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $config->delete();

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config deleted successfully.');
    }

    public function toggle(Config $config)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.config.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $config->update([
            'is_active' => !$config->is_active
        ]);

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config status updated.');
    }
}
