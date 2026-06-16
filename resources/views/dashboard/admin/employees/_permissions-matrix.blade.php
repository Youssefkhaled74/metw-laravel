@php
    $selectedPermissions = collect($selectedPermissions ?? old('permissions', []))->map(fn ($permission) => (string) $permission)->all();
    $permissionGroups = collect($permissions)
        ->groupBy(function ($permission) {
            if (str_starts_with($permission->name, 'admin.commissions')) {
                return 'admin.commissions';
            }

            $parts = explode('.', $permission->name);

            if (count($parts) <= 2) {
                return $permission->name;
            }

            return implode('.', array_slice($parts, 0, -1));
        })
        ->sortKeys();

    $selectedCount = count($selectedPermissions);
@endphp

<div class="card shadow-sm border-0 role-form-card mb-3">
    <div class="card-header bg-white border-0 py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.assign_permissions') }}</h5>
                <p class="mb-0 text-muted small">{{ __('admin-dashboard.permission_matrix') }}</p>
            </div>
            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                <i class="fas fa-shield-halved me-1"></i>
                {{ __('admin-dashboard.permission_matrix') }}
            </span>
        </div>
    </div>

    <div class="card-body p-4 p-lg-5">
        <div class="permission-matrix-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="mb-1 fw-bold">{{ __('admin-dashboard.assign_permissions') }}</h6>
                <small class="text-muted" id="rolePermissionsSelectedCount">
                    {{ __('admin-dashboard.selected_permissions_count', ['count' => $selectedCount]) }}
                </small>
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" onclick="window.rolePermissionsSelectAllPermissions()">
                    {{ __('admin-dashboard.select_all') }}
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.rolePermissionsClearAllPermissions()">
                    {{ __('admin-dashboard.deselect_all') }}
                </button>
            </div>
        </div>

        <div class="permission-matrix">
            @forelse($permissionGroups as $groupKey => $groupPermissions)
                @php
                    $translatedGroupLabel = __('permissions.' . $groupKey);
                    $groupLabel = $translatedGroupLabel !== 'permissions.' . $groupKey
                        ? $translatedGroupLabel
                        : ucwords(str_replace(['admin.', '.', '-', '_'], ' ', $groupKey));
                    $groupSelectedCount = $groupPermissions->filter(function ($permission) use ($selectedPermissions) {
                        return in_array($permission->name, $selectedPermissions, true);
                    })->count();
                    $groupTotalCount = $groupPermissions->count();
                    $groupIsOpen = $groupSelectedCount > 0;
                @endphp
                <details class="permission-group card border-0 shadow-sm mb-3" {{ $groupIsOpen ? 'open' : '' }}>
                    <summary class="permission-group-summary card-header bg-light border-0 d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="permission-group-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $groupLabel }}</h6>
                                <small class="text-muted">{{ __('admin-dashboard.permissions') }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    onclick="event.preventDefault(); event.stopPropagation(); window.rolePermissionsToggleGroup('{{ $groupKey }}')">
                                {{ __('admin-dashboard.select_all') }}
                            </button>
                            <span class="badge bg-primary-subtle text-primary">{{ $groupSelectedCount }} / {{ $groupTotalCount }}</span>
                        </div>
                    </summary>
                    <div class="card-body permission-group-body">
                        <div class="row g-3">
                            @foreach($groupPermissions as $permission)
                                @php
                                    $isChecked = in_array($permission->name, $selectedPermissions, true);
                                    $permissionLabel = __('permissions.' . $permission->name);
                                @endphp
                                <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                                    <label class="permission-tile form-check m-0 w-100 h-100">
                                        <input class="form-check-input permission-checkbox"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               data-group="{{ $groupKey }}"
                                               onchange="window.rolePermissionsToggleCheckbox(this)"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <span class="permission-tile-inner d-block h-100">
                                            <span class="permission-name d-block">{{ $permissionLabel }}</span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>
            @empty
                <div class="alert alert-light border text-center mb-0">
                    {{ __('admin-dashboard.no_permissions_found') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .permission-matrix .permission-group {
        border-radius: 16px;
        overflow: hidden;
    }

    .permission-group > summary {
        list-style: none;
        cursor: pointer;
        user-select: none;
    }

    .permission-group > summary .btn {
        position: relative;
        z-index: 2;
    }

    .permission-group > summary::-webkit-details-marker {
        display: none;
    }

    .permission-group-arrow {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(79, 70, 229, 0.12);
        color: #4f46e5;
        transition: transform .2s ease;
        flex: 0 0 auto;
    }

    .permission-group[open] .permission-group-arrow {
        transform: rotate(180deg);
    }

    .permission-group-body {
        border-top: 1px solid #e5e7eb;
    }

    .permission-group summary .badge {
        white-space: normal;
        line-height: 1.25;
    }

    .permission-tile {
        display: block;
        cursor: pointer;
        position: relative;
    }

    .permission-tile input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .permission-tile-inner {
        height: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: .9rem 1rem;
        background: #fff;
        transition: all .2s ease;
    }

    .permission-tile:hover .permission-tile-inner {
        border-color: #c7d2fe;
        box-shadow: 0 10px 18px rgba(79, 70, 229, 0.08);
        transform: translateY(-1px);
    }

    .permission-tile input:checked + .permission-tile-inner {
        border-color: #4f46e5;
        background: linear-gradient(180deg, rgba(79, 70, 229, 0.09), rgba(79, 70, 229, 0.04));
        box-shadow: 0 12px 22px rgba(79, 70, 229, 0.12);
    }

    .permission-name {
        font-weight: 600;
        color: #111827;
        line-height: 1.45;
    }

    .permission-matrix-toolbar {
        padding: .9rem 1rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
    }
</style>
