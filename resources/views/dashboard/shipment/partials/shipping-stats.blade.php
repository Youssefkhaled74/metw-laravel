<!-- Total Shipments -->
<div class="col-xl-3 col-md-6">
    <div class="card top-kpi-card top-kpi-card--primary">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div>
                    <div class="top-kpi-label mb-1">{{ __('shipment-dashboard.total_shipments') }}</div>
                    <div class="top-kpi-value mb-0">{{ number_format($stats['total_orders']) }}</div>
                </div>
                <div class="top-kpi-icon">
                    <i class="fas fa-boxes-stacked stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivered -->
<div class="col-xl-3 col-md-6">
    <div class="card top-kpi-card top-kpi-card--success">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div>
                    <div class="top-kpi-label mb-1">{{ __('shipment-dashboard.delivered') }}</div>
                    <div class="top-kpi-value mb-0">{{ number_format($stats['delivered_orders']) }}</div>
                </div>
                <div class="top-kpi-icon">
                    <i class="fas fa-circle-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending -->
<div class="col-xl-3 col-md-6">
    <div class="card top-kpi-card top-kpi-card--warning">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div>
                    <div class="top-kpi-label mb-1">{{ __('shipment-dashboard.pending') }}</div>
                    <div class="top-kpi-value mb-0">{{ number_format($stats['pending_orders'] + $stats['ecommerce_pending_orders']) }}</div>
                </div>
                <div class="top-kpi-icon">
                    <i class="fas fa-hourglass-half stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>
