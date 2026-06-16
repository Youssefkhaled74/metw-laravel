@extends('layouts.admin')

@section('title', __('admin-dashboard.shipment_companies_management'))
@section('page-title', __('admin-dashboard.shipment_companies_management'))

@section('page-actions')
    <a href="{{ route('admin.shipment-companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_company') }}
    </a>
@endsection

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $companies */
@endphp

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_shipment_companies') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-truck me-2"></i>
                    {{ $companies->count() }} / {{ $companies->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.shipment-companies') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            id="companiesSearch"
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الإيميل أو الهاتف أو العنوان...' : 'Search by name, email, phone, or address...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <select id="statusFilter" name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.shipment-companies') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($companies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $companyNumberDir = request('sort_by') === 'company_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.shipment-companies', array_merge(request()->except('page'), ['sort_by' => 'company_number', 'sort_dir' => $companyNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.company_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'company_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.shipment-companies', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('admin-dashboard.company_name') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.company_email') }}</th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.company_phone') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.company_status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $packagesDir = request('sort_by') === 'packages' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.shipment-companies', array_merge(request()->except('page'), ['sort_by' => 'packages', 'sort_dir' => $packagesDir])) }}">
                                        <span>{{ __('admin-dashboard.company_packages') }}</span>
                                        <i class="fas {{ request('sort_by') === 'packages' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $ordersDir = request('sort_by') === 'orders' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.shipment-companies', array_merge(request()->except('page'), ['sort_by' => 'orders', 'sort_dir' => $ordersDir])) }}">
                                        <span>{{ __('admin-dashboard.company_orders') }}</span>
                                        <i class="fas {{ request('sort_by') === 'orders' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.shipment-companies', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.company_created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.company_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $company->company_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if ($company->logo)
                                                <img src="{{ asset($company->logo) }}" alt="{{ $company->name }}" class="rounded-circle entity-logo" width="42" height="42">
                                            @else
                                                <div class="entity-avatar">
                                                    <i class="fas fa-truck"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark entity-name">{{ $company->name }}</div>
                                                @if ($company->description)
                                                    <small class="text-muted d-block entity-subname">{{ \Illuminate\Support\Str::limit($company->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="mobile-hide">
                                        <a href="mailto:{{ $company->email }}" class="text-decoration-none">{{ $company->email }}</a>
                                    </td>
                                    <td class="mobile-hide">
                                        <a href="tel:{{ $company->phone }}" class="text-decoration-none">{{ $company->phone }}</a>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $company->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $company->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $company->is_active ? __('admin-dashboard.company_active') : __('admin-dashboard.company_inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="count-pill packages-pill">{{ $company->packages_count }}</span>
                                    </td>
                                    <td>
                                        <span class="count-pill orders-pill">{{ $company->orders_count }}</span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $company->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.shipment-companies.show', $company->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.company_view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.shipment-companies.toggle-status', $company->id) }}" class="d-inline m-0" onsubmit="return confirm('{{ $company->is_active ? __('admin-dashboard.company_confirm_deactivate') : __('admin-dashboard.company_confirm_activate') }}')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $company->is_active ? 'warning' : 'success' }} text-white action-icon-btn" title="{{ $company->is_active ? __('admin-dashboard.deactivate_company') : __('admin-dashboard.activate_company') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $company->is_active ? 'pause' : 'play' }}"></i>
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
                    {{ $companies->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-truck empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_companies_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_companies_message') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.shipment-companies.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_first_company') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
