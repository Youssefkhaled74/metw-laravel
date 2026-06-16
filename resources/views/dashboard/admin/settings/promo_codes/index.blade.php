@extends('layouts.admin')

@section('title', __('admin-dashboard.promo_codes_management'))
@section('page-title', __('admin-dashboard.promo_codes_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.promo_codes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_promo_code') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_promo_codes') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-ticket-alt me-2"></i>
                    {{ $promo_codes->count() }} / {{ $promo_codes->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.promo_codes.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالكود أو النوع...' : 'Search by code or type...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="discount_type" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('discount_type', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل Discount Type' : 'All discount types' }}</option>
                        <option value="percentage" {{ request('discount_type') === 'percentage' ? 'selected' : '' }}>{{ __('admin-dashboard.percentage') }}</option>
                        <option value="fixed" {{ request('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('admin-dashboard.fixed') }}</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="type" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الأنواع' : 'All types' }}</option>
                        <option value="shipment" {{ request('type') === 'shipment' ? 'selected' : '' }}>{{ __('admin-dashboard.shipment') }}</option>
                        <option value="ecommerce" {{ request('type') === 'ecommerce' ? 'selected' : '' }}>{{ __('admin-dashboard.ecommerce') }}</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <input type="date" name="valid_from" class="form-control form-control-sm search-input-modern" value="{{ request('valid_from') }}" placeholder="{{ __('admin-dashboard.valid_from') }}">
                </div>

                <div class="col-lg-2">
                    <input type="date" name="valid_to" class="form-control form-control-sm search-input-modern" value="{{ request('valid_to') }}" placeholder="{{ __('admin-dashboard.valid_to') }}">
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.settings.promo_codes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($promo_codes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $codeDir = request('sort_by') === 'code' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'code', 'sort_dir' => $codeDir])) }}">
                                        <span>{{ __('admin-dashboard.code') }}</span>
                                        <i class="fas {{ request('sort_by') === 'code' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.discount_type') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $discountValueDir = request('sort_by') === 'discount_value' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'discount_value', 'sort_dir' => $discountValueDir])) }}">
                                        <span>{{ __('admin-dashboard.discount_value') }}</span>
                                        <i class="fas {{ request('sort_by') === 'discount_value' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $validFromDir = request('sort_by') === 'valid_from' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'valid_from', 'sort_dir' => $validFromDir])) }}">
                                        <span>{{ __('admin-dashboard.valid_from') }}</span>
                                        <i class="fas {{ request('sort_by') === 'valid_from' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $validToDir = request('sort_by') === 'valid_to' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'valid_to', 'sort_dir' => $validToDir])) }}">
                                        <span>{{ __('admin-dashboard.valid_to') }}</span>
                                        <i class="fas {{ request('sort_by') === 'valid_to' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $maxUsesDir = request('sort_by') === 'max_uses' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'max_uses', 'sort_dir' => $maxUsesDir])) }}">
                                        <span>{{ __('admin-dashboard.max_uses') }}</span>
                                        <i class="fas {{ request('sort_by') === 'max_uses' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $userMaxUsesDir = request('sort_by') === 'user_max_uses' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'user_max_uses', 'sort_dir' => $userMaxUsesDir])) }}">
                                        <span>{{ __('admin-dashboard.user_max_uses') }}</span>
                                        <i class="fas {{ request('sort_by') === 'user_max_uses' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $usesDir = request('sort_by') === 'uses' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'uses', 'sort_dir' => $usesDir])) }}">
                                        <span>{{ __('admin-dashboard.uses') }}</span>
                                        <i class="fas {{ request('sort_by') === 'uses' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.type') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.promo_codes.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promo_codes as $promo_code)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $promo_code->code }}</td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                                            {{ $promo_code->discount_type }}
                                        </span>
                                    </td>
                                    <td>{{ $promo_code->discount_value }}</td>
                                    <td>@include('admin.partials.date', ['date' => $promo_code->valid_from])</td>
                                    <td>@include('admin.partials.date', ['date' => $promo_code->valid_to])</td>
                                    <td>
                                        <span class="count-pill packages-pill">{{ $promo_code->max_uses ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="count-pill orders-pill">{{ $promo_code->user_max_uses ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="count-pill packages-pill">{{ $promo_code->uses }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.promo_codes.toggle-status', $promo_code->id) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $promo_code->is_active ? 'success' : 'danger' }} text-white action-icon-btn" title="{{ $promo_code->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-{{ $promo_code->is_active ? 'check' : 'times' }}"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                                            {{ $promo_code->type }}
                                        </span>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $promo_code->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.settings.promo_codes.edit', $promo_code->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.promo_codes.destroy', $promo_code->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_promo') }}');">
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
                    {{ $promo_codes->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-ticket-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_promo_codes_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_promo_codes_message') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.promo_codes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_promo_code') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
