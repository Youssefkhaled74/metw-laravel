<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
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
        if ($response = $this->authorizeEmployee('admin.permissions.index')) {
            return $response;
        }

        $permissions = Permission::where('guard_name', 'employee')
            ->orderBy('name')
            ->paginate(10);

        return view('dashboard.admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        if ($response = $this->authorizeEmployee('admin.permissions.create')) {
            return $response;
        }

        return view('dashboard.admin.permissions.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.store')) {
            return $response;
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:permissions,name',
            'label_en'  => 'required|string|max:255',
            'label_ar'  => 'required|string|max:255',
        ]);

        Permission::create([
            'name' => $data['name'],
            'guard_name' => 'employee',
        ]);

        $this->updatePermissionTranslations($data['name'], $data['label_en'], $data['label_ar']);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission created successfully.'));
    }

    /**
     * Add the permission label to EN & AR lang files if not already present.
     */
    protected function updatePermissionTranslations(string $name, ?string $labelEn, ?string $labelAr, bool $overwrite = false): void
    {
        // Lang files live under base_path('lang'), not resources path
        if ($labelEn !== null && $labelEn !== '') {
            $this->updateLangPermissionFile(base_path('lang/en/permissions.php'), $name, $labelEn, $overwrite);
        }

        if ($labelAr !== null && $labelAr !== '') {
            $this->updateLangPermissionFile(base_path('lang/ar/permissions.php'), $name, $labelAr, $overwrite);
        }
    }

    protected function updateLangPermissionFile(string $path, string $key, string $value, bool $overwrite = false): void
    {
        if (!file_exists($path) || $value === '') {
            return;
        }

        $data = include $path;
        if (!is_array($data)) {
            $data = [];
        }

        if (array_key_exists($key, $data) && ! $overwrite) {
            // Key already exists and we're not allowed to overwrite
            return;
        }

        $data[$key] = $value;

        $export = var_export($data, true);
        $content = "<?php\n// This file is automatically updated when new permissions are created.\nreturn " . $export . ";\n";

        file_put_contents($path, $content);
    }

    public function show(Permission $permission)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.show')) {
            return $response;
        }

        if ($permission->guard_name !== 'employee') {
            abort(404);
        }

        return view('dashboard.admin.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.edit')) {
            return $response;
        }

        if ($permission->guard_name !== 'employee') {
            abort(404);
        }

        $labelEn = $this->getPermissionLabel(base_path('lang/en/permissions.php'), $permission->name);
        $labelAr = $this->getPermissionLabel(base_path('lang/ar/permissions.php'), $permission->name);

        return view('dashboard.admin.permissions.edit', [
            'permission' => $permission,
            'labelEn' => $labelEn,
            'labelAr' => $labelAr,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.update')) {
            return $response;
        }

        if ($permission->guard_name !== 'employee') {
            abort(404);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'label_en'  => 'nullable|string|max:255',
            'label_ar'  => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $data['name'],
        ]);

        // If labels provided, update translations as well
        $this->updatePermissionTranslations(
            $data['name'],
            $data['label_en'] ?? null,
            $data['label_ar'] ?? null,
            true // allow overwrite when editing
        );

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission updated successfully.'));
    }

    /**
     * Helper to read a permission label from a lang/permissions.php file.
     */
    protected function getPermissionLabel(string $path, string $key): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $data = include $path;
        if (!is_array($data)) {
            return null;
        }

        return $data[$key] ?? null;
    }

    public function destroy(Permission $permission)
    {
        if ($response = $this->authorizeEmployee('admin.permissions.destroy')) {
            return $response;
        }

        if ($permission->guard_name !== 'employee') {
            abort(404);
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('Permission deleted successfully.'));
    }
}


