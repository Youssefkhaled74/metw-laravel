@extends('layouts.vendor')

@section('title', __('vendor-dashboard.dashboard'))
@section('page-title', __('vendor-dashboard.vendor_dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $totalProducts }}</h3>
                            <div>{{ __('vendor-dashboard.total_products') }}</div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('vendor.products') }}">
                        {{ __('vendor-dashboard.view_details') }}
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $activeProducts }}</h3>
                            <div>{{ __('vendor-dashboard.active_products') }}</div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('vendor.products') }}">
                        {{ __('vendor-dashboard.view_details') }}
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $totalOrders }}</h3>
                            <div>{{ __('vendor-dashboard.total_orders') }}</div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('vendor.orders') }}">
                        {{ __('vendor-dashboard.view_details') }}
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $pendingOrders }}</h3>
                            <div>{{ __('vendor-dashboard.pending_orders') }}</div>
                        </div>
                        <div class="text-white-50">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('vendor.orders') }}">
                        {{ __('vendor-dashboard.view_details') }}
                    </a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .urgent-tasks-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }

        .urgent-card {
            grid-column: span 12;
            border: 1px solid #e9edf4;
            border-radius: 1rem;
            background: linear-gradient(160deg, #ffffff 0%, #f9fbff 100%);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .urgent-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1rem .75rem;
            border-bottom: 1px dashed #e8eef7;
        }

        .urgent-card__title {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .95rem;
        }

        .urgent-card__count {
            border-radius: 999px;
            padding: .25rem .625rem;
            font-size: .75rem;
            font-weight: 700;
        }

        .urgent-card__body {
            padding: .75rem 1rem 1rem;
        }

        .urgent-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: .5rem;
        }

        .urgent-list li {
            border: 1px solid #edf2f7;
            border-radius: .75rem;
            padding: .625rem .75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            background: #fff;
        }

        .urgent-meta {
            color: #64748b;
            font-size: .8rem;
        }

        .urgent-link {
            color: #0f172a;
            text-decoration: none;
            transition: color .2s ease;
        }

        .urgent-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .urgent-empty {
            color: #64748b;
            font-size: .9rem;
            padding: .75rem;
            border: 1px dashed #d7e2f0;
            border-radius: .75rem;
            text-align: center;
            background: #fff;
        }

        .status-mini {
            border-radius: 999px;
            padding: .15rem .5rem;
            font-size: .72rem;
            font-weight: 700;
            background: #e9f2ff;
            color: #1d4ed8;
            white-space: nowrap;
        }

        @media (min-width: 992px) {
            .urgent-card {
                grid-column: span 6;
            }
        }
    </style>

    <!-- Urgent Tasks -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ __('vendor-dashboard.urgent_tasks') }}</h5>
            <span class="text-muted small">{{ __('vendor-dashboard.latest_updates') }}</span>
        </div>

        <div class="card-body">
            <div class="urgent-tasks-grid">
                <div class="urgent-card">
                    <div class="urgent-card__head">
                        <h6 class="urgent-card__title text-primary">
                            <i class="fas fa-basket-shopping"></i>
                            {{ __('vendor-dashboard.new_incomplete_sales_orders') }}
                        </h6>
                        <span class="urgent-card__count bg-primary-subtle text-primary">{{ $incompleteSalesOrders->count() }}</span>
                    </div>
                    <div class="urgent-card__body">
                        @if($incompleteSalesOrders->isNotEmpty())
                            <ul class="urgent-list">
                                @foreach($incompleteSalesOrders as $order)
                                    @php
                                        $statusRaw = $order->status;
                                        $statusValue = is_object($statusRaw) && property_exists($statusRaw, 'value') ? $statusRaw->value : $statusRaw;
                                        $statusLabel = __('vendor-dashboard.statuses.' . $statusValue);
                                        if ($statusLabel === 'vendor-dashboard.statuses.' . $statusValue) {
                                            $statusLabel = __('vendor-dashboard.' . $statusValue);
                                        }
                                        if ($statusLabel === 'vendor-dashboard.' . $statusValue) {
                                            $statusLabel = ucfirst(str_replace('_', ' ', $statusValue));
                                        }

                                        $vendorItem = $order->items->first();
                                    @endphp
                                    <li>
                                        <div>
                                            <div class="fw-semibold">
                                                <a class="urgent-link" href="{{ route('vendor.orders.show', $order->id) }}">#{{ $order->order_number }}</a>
                                            </div>
                                            <div class="urgent-meta">
                                                {{ $vendorItem->product->name ?? __('vendor-dashboard.product') }} - {{ $order->created_at?->diffForHumans() }}
                                            </div>
                                        </div>
                                        <span class="status-mini">{{ $statusLabel }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-2 text-end">
                                <a href="{{ route('vendor.orders') }}" class="small text-decoration-none">{{ __('vendor-dashboard.view_all_orders') }}</a>
                            </div>
                        @else
                            <div class="urgent-empty">{{ __('vendor-dashboard.no_new_incomplete_sales_orders') }}</div>
                        @endif
                    </div>
                </div>

                <div class="urgent-card">
                    <div class="urgent-card__head">
                        <h6 class="urgent-card__title text-danger">
                            <i class="fas fa-ban"></i>
                            {{ __('vendor-dashboard.new_incomplete_cancellation_requests') }}
                        </h6>
                        <span class="urgent-card__count bg-danger-subtle text-danger">0</span>
                    </div>
                    <div class="urgent-card__body">
                        <div class="urgent-empty">{{ __('vendor-dashboard.cancellation_requests_placeholder') }}</div>
                    </div>
                </div>

                <div class="urgent-card">
                    <div class="urgent-card__head">
                        <h6 class="urgent-card__title text-success">
                            <i class="fas fa-rotate-left"></i>
                            {{ __('vendor-dashboard.new_incomplete_return_requests') }}
                        </h6>
                        <span class="urgent-card__count bg-success-subtle text-success">{{ $incompleteReturnRequests->count() }}</span>
                    </div>
                    <div class="urgent-card__body">
                        @if($incompleteReturnRequests->isNotEmpty())
                            <ul class="urgent-list">
                                @foreach($incompleteReturnRequests as $returnRequest)
                                    @php
                                        $returnStatusValue = $returnRequest->status->value ?? $returnRequest->status;
                                        $returnStatusLabel = __('vendor-dashboard.' . $returnStatusValue);
                                        if ($returnStatusLabel === 'vendor-dashboard.' . $returnStatusValue) {
                                            $returnStatusLabel = ucfirst(str_replace('_', ' ', $returnStatusValue));
                                        }
                                    @endphp
                                    <li>
                                        <div>
                                            <div class="fw-semibold">
                                                <a class="urgent-link" href="{{ route('vendor.return-requests.show', $returnRequest->id) }}">{{ $returnRequest->return_number }}</a>
                                            </div>
                                            <div class="urgent-meta">
                                                @if($returnRequest->order)
                                                    <a class="urgent-link" href="{{ route('vendor.orders.show', $returnRequest->order->id) }}">#{{ $returnRequest->order->order_number }}</a>
                                                @else
                                                    -
                                                @endif
                                                - {{ $returnRequest->created_at?->diffForHumans() }}
                                            </div>
                                        </div>
                                        <span class="status-mini">{{ $returnStatusLabel }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-2 text-end">
                                <a href="{{ route('vendor.return-requests') }}" class="small text-decoration-none">{{ __('vendor-dashboard.return_requests') }}</a>
                            </div>
                        @else
                            <div class="urgent-empty">{{ __('vendor-dashboard.no_new_incomplete_return_requests') }}</div>
                        @endif
                    </div>
                </div>

                <div class="urgent-card">
                    <div class="urgent-card__head">
                        <h6 class="urgent-card__title text-warning-emphasis">
                            <i class="fas fa-bell"></i>
                            {{ __('vendor-dashboard.new_incoming_notifications') }}
                        </h6>
                        <span class="urgent-card__count bg-warning-subtle text-warning-emphasis">{{ $latestUnreadNotifications->count() }}</span>
                    </div>
                    <div class="urgent-card__body">
                        @if($latestUnreadNotifications->isNotEmpty())
                            <ul class="urgent-list">
                                @foreach($latestUnreadNotifications as $notification)
                                    <li>
                                        <div>
                                            <div class="fw-semibold">
                                                @if(!empty($notification->data['url']))
                                                    <a class="urgent-link" href="{{ $notification->data['url'] }}">{{ $notification->data['title'] ?? __('vendor-dashboard.notification') }}</a>
                                                @else
                                                    {{ $notification->data['title'] ?? __('vendor-dashboard.notification') }}
                                                @endif
                                            </div>
                                            <div class="urgent-meta">{{ $notification->created_at?->diffForHumans() }}</div>
                                        </div>
                                        <span class="status-mini">{{ __('vendor-dashboard.new') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-2 text-end">
                                <a href="{{ route('vendor.notifications.index') }}" class="small text-decoration-none">{{ __('vendor-dashboard.view_all') }}</a>
                            </div>
                        @else
                            <div class="urgent-empty">{{ __('vendor-dashboard.no_new_notifications') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
