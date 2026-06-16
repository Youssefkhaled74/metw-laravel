<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    protected function authorizeEmployee(string $permission)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can($permission)) {
            return view('dashboard.admin.no-permission');
        }

        return null;
    }

    public function index()
    {
        try {
            if ($response = $this->authorizeEmployee('admin.roles.index')) {
                return $response;
            }

            $validated = request()->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'sort_by' => ['nullable', 'in:id,name'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'name';
            $sortDir = $validated['sort_dir'] ?? 'asc';

            $rolesQuery = Role::where('guard_name', 'employee')
                ->withCount('users');

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $rolesQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('guard_name', 'like', "%{$search}%");
                });
            }

            $roles = $rolesQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(10)
                ->appends(request()->query());

            return view('dashboard.admin.roles.index', compact('roles', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function create()
    {
        if ($response = $this->authorizeEmployee('admin.roles.create')) {
            return $response;
        }

        $permissions = Permission::where('guard_name', 'employee')
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if ($response = $this->authorizeEmployee('admin.roles.store')) {
            return $response;
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'employee',
        ]);

        if (!empty($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])
                ->where('guard_name', 'employee')
                ->get();
            $role->syncPermissions($permissions);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role created successfully.'));
    }

    public function show(Role $role)
    {
        if ($response = $this->authorizeEmployee('admin.roles.show')) {
            return $response;
        }

        if ($role->guard_name !== 'employee') {
            abort(404);
        }

        $role->load('permissions', 'users');

        return view('dashboard.admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        if ($response = $this->authorizeEmployee('admin.roles.edit')) {
            return $response;
        }

        if ($role->guard_name !== 'employee') {
            abort(404);
        }

        $permissions = Permission::where('guard_name', 'employee')
            ->orderBy('name')
            ->get();

        $role->load('permissions');

        return view('dashboard.admin.roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($response = $this->authorizeEmployee('admin.roles.update')) {
            return $response;
        }

        if ($role->guard_name !== 'employee') {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role->update([
            'name' => $data['name'],
        ]);

        $permissions = [];
        if (!empty($data['permissions'])) {
            $permissions = Permission::whereIn('name', $data['permissions'])
                ->where('guard_name', 'employee')
                ->get();
        }

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        if ($response = $this->authorizeEmployee('admin.roles.destroy')) {
            return $response;
        }

        if ($role->guard_name !== 'employee') {
            abort(404);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('Role deleted successfully.'));
    }
}


