@extends('layouts.admin')

@section('title', __('admin-dashboard.admin_dashboard'))
@section('page-title', __('admin-dashboard.dashboard_overview'))

@section('content')
    <div class="row g-3 mb-4">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.total_users') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.total_vendors') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_vendors']) }}</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-store stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.shipment_companies') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_shipment_companies']) }}</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-truck stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.total_products') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-box stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.shipment_orders') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_shipment_orders']) }}</div>
                            <div class="stat-subtext">
                                <i class="fas fa-clock me-1"></i>
                                {{ __('admin-dashboard.pending') }}: <strong>{{ $stats['pending_shipment_orders'] }}</strong>
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-shipping-fast stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card stat-card-secondary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">{{ __('admin-dashboard.ecommerce_orders') }}</div>
                            <div class="stat-value">{{ number_format($stats['total_ecommerce_orders']) }}</div>
                            <div class="stat-subtext">
                                <i class="fas fa-clock me-1"></i>
                                {{ __('admin-dashboard.pending') }}: <strong>{{ $stats['pending_ecommerce_orders'] }}</strong>
                            </div>
                        </div>
                        <div class="stat-icon-wrapper">
                            <i class="fas fa-shopping-cart stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-chart-line me-2"></i>
                            {{ __('admin-dashboard.monthly_revenue') }}
                        </h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        {{ __('admin-dashboard.quick_actions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            {{ __('admin-dashboard.add_new_vendor') }}
                        </a>
                        <a href="{{ route('admin.shipment-companies.create') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-truck me-2"></i>
                            {{ __('admin-dashboard.add_shipment_company') }}
                        </a>
                        {{-- <a href="{{ route('admin.reports') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-chart-bar me-2"></i>
                            {{ __('admin-dashboard.view_reports') }}
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Recent Shipment Orders -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-shipping-fast me-2"></i>
                            {{ __('admin-dashboard.recent_shipment_orders') }}
                        </h6>
                        <input type="text"
                               class="form-control form-control-sm table-search-input"
                               data-table-id="admin-shipment-orders-table"
                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Search...' }}"
                               style="max-width: 200px;">
                    </div>
                </div>
                <div class="card-body">
                    @if ($recent_shipment_orders->count() > 0)
                        <div class="table-responsive">
                            <table id="admin-shipment-orders-table" class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin-dashboard.order_number') }}</th>
                                        <th>{{ __('admin-dashboard.customer') }}</th>
                                        <th>{{ __('admin-dashboard.company') }}</th>
                                        <th>{{ __('admin-dashboard.status') }}</th>
                                        <th class="text-end">{{ __('admin-dashboard.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_shipment_orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.shipment-orders.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                                    <i class="fas fa-hashtag text-muted me-1"></i>
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-muted me-1"></i>
                                                {{ $order->user->username ?? __('admin-dashboard.not_available') }}
                                            </td>
                                            <td>
                                                <i class="fas fa-truck text-muted me-1"></i>
                                                {{ $order->shipmentCompany->name ?? __('admin-dashboard.not_available') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }} px-2 py-1">
                                                    {{ app()->getLocale() === 'ar' ? __('admin-dashboard.' . $order->status->name) : ucfirst($order->status->name) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                {{ __('admin-dashboard.EGP') }}{{ number_format($order->final_price, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.shipment-orders') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                {{ __('admin-dashboard.view_all_orders') }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">{{ __('admin-dashboard.no_recent_shipment_orders') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Ecommerce Orders -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-shopping-cart me-2"></i>
                            {{ __('admin-dashboard.recent_ecommerce_orders') }}
                        </h6>
                        <input type="text"
                               class="form-control form-control-sm table-search-input"
                               data-table-id="admin-ecommerce-orders-table"
                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Search...' }}"
                               style="max-width: 200px;">
                    </div>
                </div>
                <div class="card-body">
                    @if ($recent_ecommerce_orders->count() > 0)
                        <div class="table-responsive">
                            <table id="admin-ecommerce-orders-table" class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin-dashboard.order_number') }}</th>
                                        <th>{{ __('admin-dashboard.customer') }}</th>
                                        <th>{{ __('admin-dashboard.status') }}</th>
                                        <th class="text-end">{{ __('admin-dashboard.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_ecommerce_orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.ecommerce-orders.show', $order->id) }}" class="text-decoration-none fw-semibold">
                                                    <i class="fas fa-hashtag text-muted me-1"></i>
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <i class="fas fa-user text-muted me-1"></i>
                                                {{ $order->user->username ?? __('admin-dashboard.not_available') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }} px-2 py-1">
                                                    {{ app()->getLocale() === 'ar' ? __('admin-dashboard.' . $order->status) : ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                {{ __('admin-dashboard.EGP') }}{{ number_format($order->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.ecommerce-orders') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                {{ __('admin-dashboard.view_all_orders') }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">{{ __('admin-dashboard.no_recent_ecommerce_orders') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @foreach ($monthly_revenue['shipment'] as $data)
                        '{{ $data->month }}/{{ $data->year }}',
                    @endforeach
                ],
                datasets: [{
                    label: '{{ __('admin-dashboard.shipment_revenue') }}',
                    data: [
                        @foreach ($monthly_revenue['shipment'] as $data)
                            {{ $data->total ?? 0 }},
                        @endforeach
                    ],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: '{{ __('admin-dashboard.ecommerce_revenue') }}',
                    data: [
                        @foreach ($monthly_revenue['ecommerce'] as $data)
                            {{ $data->total ?? 0 }},
                        @endforeach
                    ],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    @push('styles')
    <style>
        /* Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .stat-card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .stat-card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: #fff;
        }

        .stat-card-info {
            background: linear-gradient(135deg, #3494E6 0%, #EC6EAD 100%);
            color: #fff;
        }

        .stat-card-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
        }

        .stat-card-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: #fff;
        }

        .stat-card-secondary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: #fff;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-subtext {
            font-size: 0.75rem;
            opacity: 0.85;
            margin-top: 0.5rem;
        }

        .stat-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .stat-icon {
            font-size: 1.75rem;
            opacity: 0.9;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.5px;
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
        }

        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-search-input {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .table-search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Buttons */
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-lg:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-sm {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Badges */
        .badge {
            font-weight: 500;
            font-size: 0.8125rem;
        }

        /* Chart Area */
        .chart-area {
            position: relative;
            height: 300px;
            padding: 1rem;
        }

        /* Empty States */
        .text-center.py-5 {
            padding: 3rem 1rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
            }

            .stat-icon-wrapper {
                width: 50px;
                height: 50px;
            }

            .stat-icon {
                font-size: 1.5rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }

            .table-search-input {
                max-width: 100% !important;
                margin-top: 0.5rem;
            }
        }
    </style>
    @endpush
@endsection
