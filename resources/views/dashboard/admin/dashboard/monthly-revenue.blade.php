@extends('layouts.admin')

@section('title', __('admin-dashboard.monthly_revenue'))
@section('page-title', __('admin-dashboard.monthly_revenue'))

@section('content')
    @php
        $shipmentTotal = collect($monthly_revenue['shipment'])->sum(fn($item) => (float) ($item->total ?? 0));
        $ecommerceTotal = collect($monthly_revenue['ecommerce'])->sum(fn($item) => (float) ($item->total ?? 0));
        $overallTotal = $shipmentTotal + $ecommerceTotal;
    @endphp

    <div class="row g-3">
        <div class="col-xl-4 col-md-6">
            <div class="revenue-kpi-card revenue-kpi-primary h-100">
                <div class="revenue-kpi-label">{{ __('admin-dashboard.shipment_revenue') }}</div>
                <div class="revenue-kpi-value">{{ number_format($shipmentTotal, 2) }}</div>
                <div class="revenue-kpi-sub">{{ __('admin-dashboard.monthly_revenue') }}</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="revenue-kpi-card revenue-kpi-success h-100">
                <div class="revenue-kpi-label">{{ __('admin-dashboard.ecommerce_revenue') }}</div>
                <div class="revenue-kpi-value">{{ number_format($ecommerceTotal, 2) }}</div>
                <div class="revenue-kpi-sub">{{ __('admin-dashboard.monthly_revenue') }}</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="revenue-kpi-card revenue-kpi-total h-100">
                <div class="revenue-kpi-label">{{ __('admin-dashboard.total_amount') }}</div>
                <div class="revenue-kpi-value">{{ number_format($overallTotal, 2) }}</div>
                <div class="revenue-kpi-sub">{{ __('admin-dashboard.monthly_revenue') }}</div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm h-100 revenue-chart-card">
                <div class="card-header bg-white border-bottom revenue-chart-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-primary revenue-chart-title">
                            <i class="fas fa-chart-line me-2"></i>
                            {{ __('admin-dashboard.monthly_revenue') }}
                        </h6>
                        <div class="revenue-filters">
                            <div class="revenue-filter-item">
                                <label for="revenueYearFilter">Year</label>
                                <select id="revenueYearFilter" class="form-select form-select-sm"></select>
                            </div>
                            <div class="revenue-filter-item">
                                <label for="revenueMonthFilter">Month</label>
                                <select id="revenueMonthFilter" class="form-select form-select-sm"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas
                            id="monthlyRevenueChart"
                            data-shipment-series='@json(collect($monthly_revenue['shipment'])->map(fn($data) => ["year" => (int) $data->year, "month" => (int) $data->month, "total" => (float) ($data->total ?? 0)])->values())'
                            data-ecommerce-series='@json(collect($monthly_revenue['ecommerce'])->map(fn($data) => ["year" => (int) $data->year, "month" => (int) $data->month, "total" => (float) ($data->total ?? 0)])->values())'
                            data-shipment-label="{{ __('admin-dashboard.shipment_revenue') }}"
                            data-ecommerce-label="{{ __('admin-dashboard.ecommerce_revenue') }}"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-page-script="admin-monthly-revenue-bootstrap">
        (function () {
            const runInit = function () {
                if (typeof initMonthlyRevenueChartInMainContent === 'function') {
                    initMonthlyRevenueChartInMainContent();
                }
            };

            runInit();
            window.requestAnimationFrame(runInit);
            window.setTimeout(runInit, 120);

            document.addEventListener('DOMContentLoaded', runInit, { once: true });
            window.addEventListener('load', runInit, { once: true });
        })();
    </script>

    <style data-page-style="admin-monthly-revenue">
        .revenue-filters {
            display: flex;
            align-items: end;
            gap: 0.7rem;
            flex-wrap: wrap;
        }

        .revenue-filter-item {
            min-width: 132px;
        }

        .revenue-filter-item label {
            display: block;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.3rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .revenue-filter-item .form-select {
            border-radius: 10px;
            border-color: #dbe3ef;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .revenue-filter-item .form-select:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.18);
        }

        .revenue-kpi-card {
            border-radius: 18px;
            padding: 1rem 1.1rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .revenue-kpi-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.02));
            pointer-events: none;
        }

        .revenue-kpi-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .revenue-kpi-success {
            background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);
        }

        .revenue-kpi-total {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        }

        .revenue-kpi-label {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.9;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .revenue-kpi-value {
            font-size: 1.75rem;
            line-height: 1.15;
            font-weight: 900;
        }

        .revenue-kpi-sub {
            margin-top: 0.35rem;
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .revenue-chart-card {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.08) !important;
        }

        .revenue-chart-header {
            border-top-left-radius: 18px !important;
            border-top-right-radius: 18px !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        .revenue-chart-title {
            font-size: 1rem;
        }

        .chart-area {
            position: relative;
            height: 420px;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .revenue-filters {
                width: 100%;
                margin-top: 0.75rem;
                justify-content: flex-start;
            }

            .revenue-filter-item {
                min-width: 118px;
            }

            .revenue-chart-header > div {
                flex-wrap: wrap;
                align-items: flex-start !important;
                gap: 0.6rem;
            }

            .revenue-kpi-label {
                font-size: 0.72rem;
            }

            .revenue-kpi-value {
                font-size: 1.45rem;
            }

            .chart-area {
                height: 320px;
                padding: 0.5rem;
            }
        }
    </style>
@endsection
