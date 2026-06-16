@extends('layouts.vendor')

@section('title', __('vendor-dashboard.branches'))
@section('page-title', __('vendor-dashboard.my_branches'))

@section('content')
    <x-admin.shared-table-assets />

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 data-card">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <h4 class="mb-0">{{ __('vendor-dashboard.branches_list') }}</h4>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                                    <i class="fas fa-code-branch me-2"></i>
                                    {{ $branches->count() }} / {{ $branches->total() }}
                                </span>
                                <a href="{{ route('vendor.branches.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_new_branch') }}
                                </a>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('vendor.branches') }}" class="row g-2 align-items-center">
                            <div class="col-lg-5">
                                <div class="input-group input-group-sm search-shell">
                                    <span class="input-group-text bg-white border-end-0 search-icon-shell">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control border-start-0 search-input-modern"
                                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الموقع أو العنوان أو رقم الفرع...' : 'Search by branch name, location, address, or ID...' }}"
                                        value="{{ request('search') }}"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <select name="status" class="form-select form-select-sm filter-select-modern">
                                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('vendor-dashboard.active') }}</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('vendor-dashboard.inactive') }}</option>
                                </select>
                            </div>

                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                            <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                            <div class="col-lg-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                                </button>
                                <a href="{{ route('vendor.branches') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0 table-wrap">
                        @if($branches->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 data-table">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap sortable-col">
                                                @php
                                                    $idDir = request('sort_by') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                                @endphp
                                                <a class="text-decoration-none text-reset" href="{{ route('vendor.branches', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                                    <span>#</span>
                                                    <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                                </a>
                                            </th>
                                            <th class="text-nowrap sortable-col">
                                                @php
                                                    $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                                @endphp
                                                <a class="text-decoration-none text-reset" href="{{ route('vendor.branches', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                                    <span>{{ __('vendor-dashboard.branch_name') }}</span>
                                                    <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                                </a>
                                            </th>
                                            <th>{{ __('vendor-dashboard.location') }}</th>
                                            <th class="mobile-hide">{{ __('vendor-dashboard.address') }}</th>
                                            <th>{{ __('vendor-dashboard.status') }}</th>
                                            <th>{{ __('vendor-dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($branches as $branch)
                                            <tr>
                                                <td class="fw-semibold text-primary">{{ $branch->id }}</td>
                                                <td class="fw-semibold text-dark">{{ $branch->name }}</td>
                                                <td>
                                                    <small class="d-block">
                                                        <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                                        {{ app()->getLocale() == 'ar'
                                                            ? optional($branch->state)->name_ar
                                                            : optional($branch->state)->name_en }}
                                                    </small>
                                                    <small class="d-block text-muted">
                                                        {{ app()->getLocale() == 'ar'
                                                            ? optional($branch->city)->name_ar
                                                            : optional($branch->city)->name_en }}
                                                    </small>
                                                </td>
                                                <td class="mobile-hide">
                                                    <small>{{ \Illuminate\Support\Str::limit($branch->street_main, 40) }}</small>
                                                </td>
                                                <td>
                                                    <span class="status-pill {{ $branch->status ? 'status-active' : 'status-inactive' }}">
                                                        <span class="status-dot {{ $branch->status ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                                        {{ $branch->status ? __('vendor-dashboard.active') : __('vendor-dashboard.inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="actions-group">
                                                        <a href="{{ route('vendor.branches.show', $branch->id) }}"
                                                           class="btn btn-sm btn-primary text-white action-icon-btn"
                                                           title="{{ __('vendor-dashboard.view') }}"
                                                           data-bs-toggle="tooltip" data-bs-placement="top">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('vendor.branches.edit', $branch->id) }}"
                                                           class="btn btn-sm btn-info text-white action-icon-btn"
                                                           title="{{ __('vendor-dashboard.edit') }}"
                                                           data-bs-toggle="tooltip" data-bs-placement="top">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form action="{{ route('vendor.branches.destroy', $branch->id) }}"
                                                              method="POST"
                                                              class="d-inline m-0"
                                                              onsubmit="return confirm('{{ __('vendor-dashboard.confirm_delete') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-danger text-white action-icon-btn"
                                                                    title="{{ __('vendor-dashboard.delete') }}"
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
                                {{ $branches->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state d-inline-block px-4 py-5">
                                    <i class="fas fa-code-branch empty-icon mb-3"></i>
                                    <h5 class="text-muted">{{ __('vendor-dashboard.no_branches_found') }}</h5>
                                    <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'لا توجد فروع مضافة حالياً.' : 'No branches found at the moment.' }}</p>
                                    <div class="mt-3">
                                        <a href="{{ route('vendor.branches.create') }}" class="btn btn-primary">
                                            {{ __('vendor-dashboard.add_first_branch') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
