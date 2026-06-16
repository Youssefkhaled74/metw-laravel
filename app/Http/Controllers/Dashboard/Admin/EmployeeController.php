<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.index')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'sort_by' => ['nullable', 'in:employee_number,hire_date,created_at'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $employeesQuery = Employee::query()->with('roles');

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $employeesQuery->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            }

            $employees = $employeesQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(10)
                ->appends($request->query());

            return view('dashboard.admin.employees.index', compact('employees', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.create')) {
            return view('dashboard.admin.no-permission');
        }
        $roles = Role::where('guard_name', 'employee')->get();
        $permissions = Permission::where('guard_name', 'employee')->get();
        return view('dashboard.admin.employees.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:employees,email',
            'phone'      => 'nullable|string|max:20',
            'position'   => 'nullable|string|max:255',
            'salary'     => 'nullable|numeric|min:0',
            'hire_date'  => 'nullable|date',
            'password'   => 'required|string|min:6|confirmed',
            'roles'      => 'nullable|array',
            'permissions'=> 'nullable|array',
        ]);

        // app()[\Spatie\Permission\PermissionRegistrar::class]->setDefaultGuardName('employee');

        $employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'position'   => $request->position,
            'salary'     => $request->salary,
            'hire_date'  => $request->hire_date,
            'password'   => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $roles = Role::whereIn('name', $request->roles)
                ->where('guard_name', 'employee')
                ->get();
            $employee->syncRoles($roles);
        }

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('name', $request->permissions)
                ->where('guard_name', 'employee')
                ->get();
            $employee->syncPermissions($permissions);
        }

        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.show')) {
            return view('dashboard.admin.no-permission');
        }
        $employee = Employee::with('roles', 'permissions')->findOrFail($id);
        return view('dashboard.admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $roles = Role::where('guard_name', 'employee')->get();
        $permissions = Permission::where('guard_name', 'employee')->get();
        return view('dashboard.admin.employees.edit', compact('employee', 'roles', 'permissions'));
    }

    public function update(Request $request, Employee $employee)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:employees,email,' . $employee->id,
            'phone'      => 'nullable|string|max:20',
            'position'   => 'nullable|string|max:255',
            'salary'     => 'nullable|numeric|min:0',
            'hire_date'  => 'nullable|date',
            'password'   => 'nullable|string|min:6|confirmed',
            'roles'      => 'nullable|array',
            'permissions'=> 'nullable|array',
        ]);

        $employee->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'position'   => $request->position,
            'salary'     => $request->salary,
            'hire_date'  => $request->hire_date,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $employee->password,
        ]);

        $employee->syncRoles($request->roles ?? []);
        $employee->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $employee->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
