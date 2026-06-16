@extends('layouts.shipment')

@section('title', __('shipment-dashboard.orders_management'))
@section('page-title', __('shipment-dashboard.orders_management'))

@section('page-actions')
    <a href="{{ route('shipment.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back_to_dashboard')
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">@lang('shipment-dashboard.all_orders')</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-truck me-2"></i>
                    {{ $orders->count() }} / {{ $orders->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('shipment.orders') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الطلب أو اسم العميل أو الإيميل...' : 'Search by order number, customer, or email...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>@lang('shipment-dashboard.pending_orders')</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>@lang('shipment-dashboard.accepted')</option>
                        <option value="pickup" {{ request('status') === 'pickup' ? 'selected' : '' }}>@lang('shipment-dashboard.pick_up')</option>
                        <option value="on_way" {{ request('status') === 'on_way' ? 'selected' : '' }}>@lang('shipment-dashboard.on_way')</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>@lang('shipment-dashboard.delivered')</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>@lang('shipment-dashboard.cancelled')</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>@lang('shipment-dashboard.returned')</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('shipment.orders') }}" class="btn btn-outline-secondary btn-sm">
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
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.orders', array_merge(request()->except('page'), ['sort_by' => 'order_number', 'sort_dir' => $orderNumberDir])) }}">
                                        <span>@lang('shipment-dashboard.order_number')</span>
                                        <i class="fas {{ request('sort_by') === 'order_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $customerDir = request('sort_by') === 'customer' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.orders', array_merge(request()->except('page'), ['sort_by' => 'customer', 'sort_dir' => $customerDir])) }}">
                                        <span>@lang('shipment-dashboard.customer')</span>
                                        <i class="fas {{ request('sort_by') === 'customer' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>@lang('shipment-dashboard.status')</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $totalPriceDir = request('sort_by') === 'total_price' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.orders', array_merge(request()->except('page'), ['sort_by' => 'total_price', 'sort_dir' => $totalPriceDir])) }}">
                                        <span>@lang('shipment-dashboard.total_price')</span>
                                        <i class="fas {{ request('sort_by') === 'total_price' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $packagesDir = request('sort_by') === 'packages' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.orders', array_merge(request()->except('page'), ['sort_by' => 'packages', 'sort_dir' => $packagesDir])) }}">
                                        <span>@lang('shipment-dashboard.packages_count')</span>
                                        <i class="fas {{ request('sort_by') === 'packages' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.orders', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>@lang('shipment-dashboard.created_at')</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>@lang('shipment-dashboard.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $statusValue = $order->status->value ?? (string) $order->status;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $order->user->username ?? 'N/A' }}
                                        @if ($order->user->email)
                                            <br><small class="text-muted">{{ $order->user->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill {{ in_array($statusValue, ['delivered']) ? 'status-active' : (in_array($statusValue, ['cancelled','returned']) ? 'status-inactive' : 'packages-pill') }}">
                                            {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                                        </span>
                                    </td>
                                    <td>{{__('admin-dashboard.EGP')}}{{ number_format($order->final_price, 2) }}</td>
                                    <td>
                                        <span class="count-pill packages-pill">{{ $order->company_packages_count ?? $order->orderItems->count() }}</span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $order->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('shipment.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-primary text-white action-icon-btn"
                                                title="@lang('shipment-dashboard.view')"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($statusValue === 'pending')
                                                <form method="POST"
                                                    action="{{ route('shipment.orders.update-status', $order->id) }}" class="d-inline m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-sm btn-success text-white action-icon-btn"
                                                        title="@lang('shipment-dashboard.accept')" data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('shipment.orders.update-status', $order->id) }}" class="d-inline m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn"
                                                        title="@lang('shipment-dashboard.cancel')" data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-warning text-white action-icon-btn" data-bs-toggle="modal"
                                                    data-bs-target="#statusModal{{ $order->id }}" title="@lang('shipment-dashboard.status')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Status Update Modal -->
                                        <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('shipment.orders.update-status', $order->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">@lang('shipment-dashboard.update_order_status')</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="status{{ $order->id }}"
                                                                    class="form-label">@lang('shipment-dashboard.status')</label>
                                                                <select class="form-select" id="status{{ $order->id }}"
                                                                    name="status" required>
                                                                    <option value="pending"
                                                                        {{ $statusValue === 'pending' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.pending_orders')</option>
                                                                    <option value="accepted"
                                                                        {{ $statusValue === 'accepted' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.accepted')</option>
                                                                    <option value="pickup"
                                                                        {{ $statusValue === 'pickup' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.pick_up')</option>
                                                                    <option value="on_way"
                                                                        {{ $statusValue === 'on_way' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.on_way')</option>
                                                                    <option value="delivered"
                                                                        {{ $statusValue === 'delivered' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.delivered')</option>
                                                                    <option value="cancelled"
                                                                        {{ $statusValue === 'cancelled' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.cancelled')</option>
                                                                    <option value="returned"
                                                                        {{ $statusValue === 'returned' ? 'selected' : '' }}>
                                                                        @lang('shipment-dashboard.returned')</option>
                                                                </select>
                                                            </div>
                                                            <p><strong>@lang('shipment-dashboard.order')</strong>
                                                                {{ $order->order_number }}</p>
                                                            <p><strong>@lang('shipment-dashboard.customer')</strong>
                                                                {{ $order->user->username ?? 'N/A' }}</p>
                                                            <p><strong>@lang('shipment-dashboard.current_status')</strong>
                                                                <span
                                                                    class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $statusValue)) }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">@lang('shipment-dashboard.cancel')</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">@lang('shipment-dashboard.update_status')</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-shopping-cart empty-icon mb-3"></i>
                        <h5 class="text-muted">@lang('shipment-dashboard.no_orders_found')</h5>
                        <p class="text-muted mb-0">@lang('shipment-dashboard.no_orders_yet')</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
