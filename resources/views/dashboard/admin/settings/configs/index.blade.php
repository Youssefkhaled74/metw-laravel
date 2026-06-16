@extends('layouts.admin')

@section('title', __('admin-dashboard.configs_management'))
@section('page-title', __('admin-dashboard.configs_management'))

@section('page-actions')
    <a href="{{ route('admin.configs.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_config') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.config_list') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-cogs me-2"></i>
                    {{ $configs->count() }} / {{ $configs->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.configs.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالـ Key أو Value أو Group أو رقم الإعداد...' : 'Search by key, value, group, or ID...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.configs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($configs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $idDir = request('sort_by') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.configs.index', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                        <span>#</span>
                                        <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $keyDir = request('sort_by') === 'key' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.configs.index', array_merge(request()->except('page'), ['sort_by' => 'key', 'sort_dir' => $keyDir])) }}">
                                        <span>{{ __('admin-dashboard.key') }}</span>
                                        <i class="fas {{ request('sort_by') === 'key' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.value') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.configs.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($configs as $config)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $config->id }}</td>
                                    <td><code>{{ $config->key }}</code></td>
                                    <td class="mobile-hide">{{ \Illuminate\Support\Str::limit($config->value, 60) ?? '-' }}</td>
                                    <td>
                                        <span class="status-pill {{ $config->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $config->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $config->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $config->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <form action="{{ route('admin.configs.toggle-status', $config->id) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($config->is_active ? 'هل تريد إيقاف هذا الإعداد؟' : 'هل تريد تفعيل هذا الإعداد؟') : ($config->is_active ? 'Are you sure you want to deactivate this config?' : 'Are you sure you want to activate this config?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $config->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                        title="{{ app()->getLocale() === 'ar' ? ($config->is_active ? 'إيقاف' : 'تفعيل') : ($config->is_active ? 'Deactivate' : 'Activate') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $config->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.configs.edit', $config->id) }}"
                                               class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}"
                                               data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.configs.destroy', $config->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn"
                                                        onclick="return confirm('{{ __('admin-dashboard.confirm_delete_config') }}')"
                                                        title="{{ __('admin-dashboard.delete') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
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
                    {{ $configs->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-cogs empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_configs_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.start_by_adding_config') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.configs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_config') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
