<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2️⃣ Collect all employee/admin route names (depending on your routes)
        // If your routes are named like "admin.dashboard" but belong to employee guard,
        // you can still collect them — just set guard_name = 'employee'
        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => str_starts_with($route->getName(), 'admin.'))
            ->pluck('action.as')
            ->filter()
            ->unique()
            ->values();

        // 3️⃣ Create permissions (ALL with guard_name = 'employee')
        foreach ($routes as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'employee',
            ]);
        }

        // 4️⃣ Create roles (ALL with guard_name = 'employee')
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'employee'],
            ['name' => 'Admin', 'guard_name' => 'employee'],
            ['name' => 'Employee', 'guard_name' => 'employee'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
        }

        // 5️⃣ Assign permissions by role
        $superAdmin = Role::where('name', 'Super Admin')->where('guard_name', 'employee')->first();
        $admin      = Role::where('name', 'Admin')->where('guard_name', 'employee')->first();
        $employee   = Role::where('name', 'Employee')->where('guard_name', 'employee')->first();

        // Super Admin gets everything
        $superAdmin->syncPermissions(Permission::where('guard_name', 'employee')->get());

        // Admin gets subset (no settings or employee management)
        $adminPermissions = Permission::where('guard_name', 'employee')
            ->where(function ($q) {
                $q->where('name', 'not like', 'admin.settings.%')
                  ->where('name', 'not like', 'admin.employees.%');
            })
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Employee gets read-only type permissions
        $employeePermissions = Permission::where('guard_name', 'employee')
            ->where(function ($q) {
                $q->where('name', 'like', 'admin.%index')
                  ->orWhere('name', 'like', 'admin.%show')
                  ->orWhere('name', 'like', 'admin.dashboard');
            })
            ->get();
        $employee->syncPermissions($employeePermissions);

        // 6️⃣ Clear cache again after seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Roles and permissions (employee guard) seeded successfully!');
    }
}
