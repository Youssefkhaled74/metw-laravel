@extends('layouts.vendor')

@section('title', __('vendor-dashboard.orders'))
@section('page-title', __('vendor-dashboard.orders_management'))

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('vendor-dashboard.all_orders') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-shopping-cart me-2"></i>
                    {{ $orders->count() }} / {{ $orders->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('vendor.orders') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الطلب أو اسم العميل أو إيميله أو SKU...' : 'Search by order number, customer, email, or SKU...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('vendor-dashboard.processing') }}</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('vendor-dashboard.shipped') }}</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('vendor-dashboard.delivered') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('vendor-dashboard.cancelled') }}</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('vendor.orders') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $orderNumberDir = request('sort_by') === 'order_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.orders', array_merge(request()->except('page'), ['sort_by' => 'order_number', 'sort_dir' => $orderNumberDir])) }}">
                                        <span>{{ __('vendor-dashboard.order_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'order_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $customerDir = request('sort_by') === 'customer' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.orders', array_merge(request()->except('page'), ['sort_by' => 'customer', 'sort_dir' => $customerDir])) }}">
                                        <span>{{ __('vendor-dashboard.customer') }}</span>
                                        <i class="fas {{ request('sort_by') === 'customer' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $itemsDir = request('sort_by') === 'your_items' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.orders', array_merge(request()->except('page'), ['sort_by' => 'your_items', 'sort_dir' => $itemsDir])) }}">
                                        <span>{{ __('vendor-dashboard.your_items') }}</span>
                                        <i class="fas {{ request('sort_by') === 'your_items' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('vendor-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $totalDir = request('sort_by') === 'your_total' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.orders', array_merge(request()->except('page'), ['sort_by' => 'your_total', 'sort_dir' => $totalDir])) }}">
                                        <span>{{ __('vendor-dashboard.your_total') }}</span>
                                        <i class="fas {{ request('sort_by') === 'your_total' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.orders', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('vendor-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $order->user->username ?? __('vendor-dashboard.not_available') }}
                                        @if ($order->user->email)
                                            <br><small class="text-muted">{{ $order->user->email }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="count-pill packages-pill">{{ $order->vendor_items_count }}</span>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $order->status === 'delivered' ? 'status-active' : ($order->status === 'cancelled' ? 'status-inactive' : 'packages-pill') }}">
                                            {{ __("vendor-dashboard.statuses.$order->status") }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($order->vendor_total ?? 0, 2) }}</strong>
                                            @if(($order->vendor_total_returned ?? 0) > 0)
                                                <small class="text-danger">
                                                    - {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($order->vendor_total_returned, 2) }} {{ __('vendor-dashboard.returned') }}
                                                </small>
                                                <small class="text-success fw-semibold">
                                                    {{__("vendor-dashboard.net_total")}}: {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($order->vendor_net_total ?? 0, 2) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $order->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('vendor.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-primary text-white action-icon-btn"
                                                title="{{ __('vendor-dashboard.view') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            <div class="d-flex justify-content-center mt-4 mb-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-shopping-cart empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('vendor-dashboard.no_orders_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('vendor-dashboard.no_orders_yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
