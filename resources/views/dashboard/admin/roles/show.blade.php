@extends('layouts.admin')

@section('title', __('admin-dashboard.role_details') ?? 'Role Details')
@section('page-title', __('admin-dashboard.role_details') ?? 'Role Details')

@section('page-actions')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
    </a>
    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
    </a>
    <form action="{{ route('admin.roles.destroy', $role->id) }}"
          method="POST" class="d-inline"
          onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_role') ?? 'Are you sure you want to delete this role?' }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
        </button>
    </form>
@endsection

@section('content')
    @php
        $permissionGroups = $role->permissions
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
    @endphp

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1">{{ __('admin-dashboard.role_details') }}</div>
                            <h4 class="mb-0 fw-bold">{{ $role->name }}</h4>
                        </div>
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                            <i class="fas fa-user-shield fs-4"></i>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('admin-dashboard.id') }}</span>
                            <strong>{{ $role->id }}</strong>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('admin-dashboard.guard') }}</span>
                            <strong>{{ $role->guard_name }}</strong>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('admin-dashboard.permissions') }}</span>
                            <strong>{{ $role->permissions->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.permissions') }}</h5>
                        <small class="text-muted">{{ __('admin-dashboard.permission_matrix') }}</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($permissionGroups->count())
                        @foreach($permissionGroups as $groupKey => $groupPermissions)
                            @php
                                $translatedGroupLabel = __('permissions.' . $groupKey);
                                $groupLabel = $translatedGroupLabel !== 'permissions.' . $groupKey
                                    ? $translatedGroupLabel
                                    : ucwords(str_replace(['admin.', '.', '-', '_'], ' ', $groupKey));
                            @endphp
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-light border-0 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold">{{ $groupLabel }}</h6>
                                    <span class="badge bg-primary">{{ $groupPermissions->count() }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        @foreach($groupPermissions as $permission)
                                            <div class="col-12 col-md-6">
                                                <div class="permission-pill border rounded-3 p-3 h-100">
                                                    <div class="fw-semibold">{{ __('permissions.' . $permission->name) }}</div>
                                                    <div class="small text-muted mt-1">{{ $permission->name }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-light border mb-0">
                            {{ __('admin-dashboard.no_permissions_assigned') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


