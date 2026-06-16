@extends('layouts.admin')

@section('title', __('admin-dashboard.roles'))
@section('page-title', __('admin-dashboard.roles_management') ?? 'Roles Management')

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_role') ?? 'Add Role' }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_roles') ?? 'All Roles' }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-user-shield me-2"></i>
                    {{ $roles->count() }} / {{ $roles->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم الرول...' : 'Search by role name...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                        <tr>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $idDir = request('sort_by') === 'id' && request('sort_dir', 'asc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.roles.index', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                    <span>#</span>
                                    <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'asc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $nameDir = request('sort_by') === 'name' && request('sort_dir', 'asc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.roles.index', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                    <span>{{ __('admin-dashboard.name') ?? 'Name' }}</span>
                                    <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'asc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap">{{ __('admin-dashboard.guard') ?? 'Guard' }}</th>
                            <th class="text-nowrap">{{ __('admin-dashboard.users_count') ?? 'Users Count' }}</th>
                            <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="fw-semibold text-primary">{{ $role->id }}</td>
                                <td>
                                    <div class="fw-semibold text-dark entity-name">{{ $role->name }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">{{ $role->guard_name }}</span>
                                </td>
                                <td>
                                    <span class="count-pill packages-pill">{{ $role->users_count }}</span>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_role') ?? 'Are you sure you want to delete this role?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn" title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $roles->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-user-shield empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_roles_found') ?? 'No roles found' }}</h5>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection


