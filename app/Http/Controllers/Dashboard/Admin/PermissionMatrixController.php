<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionMatrixController extends Controller
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

    public function index(Request $request)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.index')) {
            return $response;
        }

        $search = $request->input('search', '');

        $employeesQuery = Employee::query()
            ->select('id', 'first_name', 'last_name', 'email', 'employee_number', 'position')
            ->with('permissions:id,name')
            ->orderBy('first_name');

        if ($search) {
            $employeesQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $employeesQuery->get();

        $permissions = Permission::where('guard_name', 'employee')
            ->orderBy('name')
            ->get();

        $permissionGroups = $permissions->groupBy(function ($permission) {
            if (str_starts_with($permission->name, 'admin.commissions')) {
                return 'admin.commissions';
            }

            $parts = explode('.', $permission->name);

            if (count($parts) <= 2) {
                return $permission->name;
            }

            return implode('.', array_slice($parts, 0, -1));
        })->sortKeys();

        $employeePermissionMap = [];
        foreach ($employees as $employee) {
            $employeePermissionMap[$employee->id] = $employee->permissions->pluck('name')->toArray();
        }

        return view('dashboard.admin.permission-matrix.index', compact(
            'employees',
            'permissions',
            'permissionGroups',
            'employeePermissionMap',
            'search'
        ));
    }

    public function update(Request $request)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.update')) {
            return $response;
        }

        $data = $request->validate([
            'matrix' => 'required|array',
            'matrix.*' => 'nullable|array',
            'matrix.*.*' => 'string',
        ]);

        DB::beginTransaction();

        try {
            $allPermissionNames = Permission::where('guard_name', 'employee')
                ->pluck('name')
                ->toArray();

            foreach ($data['matrix'] as $employeeId => $permissionNames) {
                $employee = Employee::find($employeeId);

                if (!$employee) {
                    continue;
                }

                $validPermissions = array_filter(
                    (array) $permissionNames,
                    fn($name) => in_array($name, $allPermissionNames, true)
                );

                $permissions = Permission::whereIn('name', $validPermissions)
                    ->where('guard_name', 'employee')
                    ->get();

                $employee->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()
                ->route('admin.permission-matrix.index')
                ->with('success', __('admin-dashboard.permission_matrix_updated'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('admin-dashboard.permission_matrix_error'));
        }
    }
}
