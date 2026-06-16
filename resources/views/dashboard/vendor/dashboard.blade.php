@extends('layouts.vendor')

@section('title', __('vendor-dashboard.vendor_dashboard'))
@section('page-title', __('vendor-dashboard.dashboard_overview'))

@push('styles')
<style>
    .vendor-dashboard .stat-card-products { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    .vendor-dashboard .stat-card-orders { background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%); }
    .vendor-dashboard .stat-card-revenue { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .vendor-dashboard .stat-card-sold { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
    .vendor-dashboard .stat-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .vendor-dashboard .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.15); }
    .vendor-dashboard .stat-card .stat-icon-wrap { width: 52px; height: 52px; border-radius: 14px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; }
    .vendor-dashboard .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.02em; }
    .vendor-dashboard .stat-card .stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.9; }
    .vendor-dashboard .stat-card .stat-meta { font-size: 0.8rem; opacity: 0.85; margin-top: 0.25rem; }
    .vendor-dashboard .section-card { border-radius: 14px; overflow: hidden; }
    .vendor-dashboard .section-card .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 1rem 1.25rem; font-weight: 600; color: #1e293b; }
    .vendor-dashboard .dashboard-table { border-collapse: separate; border-spacing: 0; }
    .vendor-dashboard .dashboard-table thead th { background: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.25rem; border: none; }
    .vendor-dashboard .dashboard-table tbody td { padding: 1rem 1.25rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .vendor-dashboard .dashboard-table tbody tr:hover { background: #f8fafc; }
    .vendor-dashboard .dashboard-table tbody tr:last-child td { border-bottom: none; }
    .vendor-dashboard .quick-action-btn { border-radius: 12px; padding: 0.85rem 1.25rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.6rem; transition: all 0.25s ease; border: none; text-decoration: none; }
    .vendor-dashboard .quick-action-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
    .vendor-dashboard .quick-action-btn i { font-size: 1.1rem; }
    .vendor-dashboard .product-thumb { width: 44px; height: 44px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; }
    .vendor-dashboard .empty-state { padding: 3rem 2rem; text-align: center; color: #94a3b8; }
    .vendor-dashboard .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="vendor-dashboard">
    <div class="row g-4 mb-4">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-products h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">{{ __('vendor-dashboard.total_products') }}</div>
                        <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                        <div class="stat-meta">{{ __('vendor-dashboard.active') }}: {{ $stats['active_products'] }}</div>
                    </div>
                    <div class="stat-icon-wrap">
                        <i class="fas fa-box stat-icon" style="font-size: 1.4rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-orders h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">{{ __('vendor-dashboard.total_orders') }}</div>
                        <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
                        <div class="stat-meta">{{ __('vendor-dashboard.pending') }}: {{ $stats['pending_orders'] }}</div>
                    </div>
                    <div class="stat-icon-wrap">
                        <i class="fas fa-shopping-cart stat-icon" style="font-size: 1.4rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-revenue h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">{{ __('vendor-dashboard.total_revenue') }}</div>
                        <div class="stat-value">{{__('admin-dashboard.EGP')}}{{ number_format($stats['total_revenue'], 2) }}</div>
                    </div>
                    <div class="stat-icon-wrap">
                        <i class="fas fa-coins stat-icon" style="font-size: 1.4rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-sold h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">{{ __('vendor-dashboard.total_sold') }}</div>
                        <div class="stat-value">{{ number_format($stats['total_sold']) }}</div>
                    </div>
                    <div class="stat-icon-wrap">
                        <i class="fas fa-chart-line stat-icon" style="font-size: 1.4rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Top Selling Products -->
        <div class="col-lg-8">
            <div class="card section-card shadow-sm">
                <div class="card-header py-3 table-card-header">
                    <h6 class="m-0 fw-semibold">{{ __('vendor-dashboard.top_selling_products') }}</h6>
                    <input type="text"
                           class="form-control form-control-sm table-search-input"
                           data-table-id="vendor-top-products-table"
                           placeholder="Search in table...">
                </div>
                <div class="card-body p-0">
                    @if ($top_products->count() > 0)
                        <div class="table-responsive">
                            <table id="vendor-top-products-table" class="table dashboard-table align-middle mb-0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('vendor-dashboard.product') }}</th>
                                        <th>{{ __('vendor-dashboard.price') }}</th>
                                        <th>{{ __('vendor-dashboard.sold') }}</th>
                                        <th>{{ __('vendor-dashboard.revenue') }}</th>
                                        <th>{{ __('vendor-dashboard.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($top_products as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if ($product->media->count() > 0)
                                                        <img src="{{ asset($product->media->first()->url) }}"
                                                            alt="{{ $product->name }}" class="product-thumb">
                                                    @else
                                                        <div class="product-thumb bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <strong class="text-dark">{{ $product->name }}</strong>
                                                </div>
                                            </td>
                                            <td><span class="fw-medium">{{__('admin-dashboard.EGP')}}{{ number_format($product->price, 2) }}</span></td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">{{ $product->sold_count }}</span></td>
                                            <td><span class="text-success fw-semibold">{{__('admin-dashboard.EGP')}}{{ number_format($product->price * $product->sold_count, 2) }}</span></td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $product->is_active ? 'success' : 'danger' }} bg-opacity-10 text-{{ $product->is_active ? 'success' : 'danger' }}">
                                                    {{ $product->is_active ? __('vendor-dashboard.active') : __('vendor-dashboard.inactive') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-box-open d-block"></i>
                            <p class="mb-0">{{ __('vendor-dashboard.no_products_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card section-card shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-semibold">{{ __('vendor-dashboard.quick_actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('vendor.products.create') }}" class="quick-action-btn btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_new_product') }}
                        </a>
                        <a href="{{ route('vendor.orders') }}" class="quick-action-btn btn btn-success">
                            <i class="fas fa-list"></i> {{ __('vendor-dashboard.view_all_orders') }}
                        </a>
                        <a href="{{ route('vendor.reports') }}" class="quick-action-btn btn btn-info">
                            <i class="fas fa-chart-bar"></i> {{ __('vendor-dashboard.view_reports') }}
                        </a>
                        <a href="{{ route('vendor.profile') }}" class="quick-action-btn btn btn-warning text-dark">
                            <i class="fas fa-user"></i> {{ __('vendor-dashboard.update_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-12">
            <div class="card section-card shadow-sm">
                <div class="card-header py-3 table-card-header">
                    <h6 class="m-0 fw-semibold">{{ __('vendor-dashboard.recent_orders') }}</h6>
                    <input type="text"
                           class="form-control form-control-sm table-search-input"
                           data-table-id="vendor-recent-orders-table"
                           placeholder="Search in table...">
                </div>
                <div class="card-body p-0">
                    @if ($recent_orders->count() > 0)
                        <div class="table-responsive">
                            <table id="vendor-recent-orders-table" class="table dashboard-table align-middle mb-0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('vendor-dashboard.order_number') }}</th>
                                        <th>{{ __('vendor-dashboard.customer') }}</th>
                                        <th>{{ __('vendor-dashboard.status') }}</th>
                                        <th>{{ __('vendor-dashboard.amount') }}</th>
                                        <th>{{ __('vendor-dashboard.created_at') }}</th>
                                        <th>{{ __('vendor-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('vendor.orders.show', $order->id) }}" class="text-decoration-none fw-semibold text-dark">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->user->username ?? __('vendor-dashboard.not_available') }}</td>
                                            <td>
                                                @php
                                                    $statusColor = match($order->status) {
                                                        'delivered' => 'success',
                                                        'pending' => 'warning',
                                                        'cancelled' => 'danger',
                                                        default => 'info'
                                                    };
                                                @endphp
                                                <span class="badge rounded-pill bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                                    {{ ucfirst(__("vendor-dashboard.statuses.$order->status")) }}
                                                </span>
                                            </td>
                                            <td><span class="fw-semibold">{{__('admin-dashboard.EGP')}}{{ number_format($order->total_amount, 2) }}</span></td>
                                            <td><span class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</span></td>
                                            <td>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('vendor.orders.show', $order->id) }}"
                                                        class="btn btn-sm btn-primary rounded-pill px-3">
                                                        <i class="fas fa-eye me-1"></i>{{ __('vendor-dashboard.view') }}
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#statusModal{{ $order->id }}">
                                                        <i class="fas fa-edit me-1"></i>{{ __('vendor-dashboard.status') }}
                                                    </button>
                                                </div>
                                                <!-- Status Update Modal -->
                                                <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg rounded-3">
                                                            <form method="POST"
                                                                action="{{ route('vendor.orders.update-status', $order->id) }}">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-header border-0 pb-0">
                                                                    <h5 class="modal-title fw-semibold">{{ __('vendor-dashboard.update_order_status') }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body pt-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-medium">{{ __('vendor-dashboard.status') }}</label>
                                                                        <select class="form-select rounded-2" name="status" required>
                                                                            @foreach (['pending','confirmed','shipped','delivered','cancelled'] as $status)
                                                                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                                                    {{ __("vendor-dashboard.statuses.$status") }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-medium">{{ __('vendor-dashboard.tracking_number') }}</label>
                                                                        <input type="text" class="form-control rounded-2"
                                                                            name="tracking_number"
                                                                            value="{{ $order->tracking_number }}">
                                                                    </div>
                                                                    <div class="bg-light rounded-2 p-3 small">
                                                                        <p class="mb-1"><strong>{{ __('vendor-dashboard.order') }}:</strong> {{ $order->order_number }}</p>
                                                                        <p class="mb-1"><strong>{{ __('vendor-dashboard.customer') }}:</strong> {{ $order->user->username ?? __('vendor-dashboard.not_available') }}</p>
                                                                        <p class="mb-0"><strong>{{ __('vendor-dashboard.current_status') }}:</strong>
                                                                            <span class="badge bg-secondary">{{ ucfirst(__("vendor-dashboard.statuses.$order->status")) }}</span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-0">
                                                                    <button type="button" class="btn btn-secondary rounded-2" data-bs-dismiss="modal">{{ __('vendor-dashboard.cancel') }}</button>
                                                                    <button type="submit" class="btn btn-primary rounded-2">{{ __('vendor-dashboard.update_status') }}</button>
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
                        <div class="text-center py-4 border-top bg-light">
                            <a href="{{ route('vendor.orders') }}" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-list me-2"></i>{{ __('vendor-dashboard.view_all_orders') }}
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart d-block"></i>
                            <p class="mb-0">{{ __('vendor-dashboard.no_recent_orders') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
