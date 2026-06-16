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

    <div class="dashboard-cycle-section mb-4" id="approvalCycles">
        <div class="cycle-section-header mb-3">
            <h5 class="mb-1 fw-bold">{{ $dashboardCycles['approvals']['title'] }}</h5>
            <p class="mb-0 text-muted small">{{ $dashboardCycles['approvals']['subtitle'] }}</p>
        </div>
        <div class="row g-3">
            @foreach ($dashboardCycles['approvals']['cards'] as $card)
                <div class="col-xl-4 col-md-6">
                    <div class="cycle-card h-100">
                        <div class="cycle-card-header">
                            <div class="cycle-card-heading">
                                <div class="cycle-card-title">{{ $card['title'] }}</div>
                                <div class="cycle-card-count">{{ number_format($card['count']) }}</div>
                            </div>
                            <span class="cycle-badge cycle-badge-warning">{{ $cycleUiLabels['needs_approval'] }}</span>
                        </div>
                        <div class="cycle-latest">
                            <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] }}</div>
                            <div class="cycle-latest-title">
                                @if (!empty($card['latest_url']))
                                    <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] }}</a>
                                @else
                                    {{ $card['latest_title'] }}
                                @endif
                            </div>
                            @if (!empty($card['latest_meta']))
                                <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                            @endif
                        </div>
                        <div class="cycle-card-footer mt-3">
                            <span class="text-muted small cycle-status-text">{{ $card['latest_status'] }}</span>
                            <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="dashboard-cycle-section mb-4" id="trustCycles">
        <div class="cycle-section-header mb-3">
            <h5 class="mb-1 fw-bold">{{ $dashboardCycles['trust']['title'] }}</h5>
            <p class="mb-0 text-muted small">{{ $dashboardCycles['trust']['subtitle'] }}</p>
        </div>
        <div class="row g-3">
            @foreach ($dashboardCycles['trust']['cards'] as $card)
                <div class="col-xl-4 col-md-6">
                    <div class="cycle-card h-100">
                        <div class="cycle-card-header">
                            <div class="cycle-card-heading">
                                <div class="cycle-card-title">{{ $card['title'] }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="cycle-mini-stat"><strong>{{ number_format($card['trusted_count']) }}</strong> {{ $cycleUiLabels['trusted'] }}</span>
                                    <span class="cycle-mini-stat"><strong>{{ number_format($card['rejected_count']) }}</strong> {{ $cycleUiLabels['rejected'] }}</span>
                                </div>
                            </div>
                            <span class="cycle-badge cycle-badge-success">{{ $cycleUiLabels['trust_level'] }}</span>
                        </div>
                        <div class="cycle-latest mb-2">
                            <div class="cycle-latest-label">{{ $cycleUiLabels['latest_trusted'] }}</div>
                            <div class="cycle-latest-title">
                                @if (!empty($card['trusted_url']))
                                    <a href="{{ $card['trusted_url'] }}">{{ $card['trusted_title'] }}</a>
                                @else
                                    {{ $card['trusted_title'] }}
                                @endif
                            </div>
                            @if (!empty($card['trusted_meta']))
                                <div class="cycle-latest-meta">{{ $card['trusted_meta'] }}</div>
                            @endif
                        </div>
                        <div class="cycle-latest">
                            <div class="cycle-latest-label">{{ $cycleUiLabels['latest_rejected'] }}</div>
                            <div class="cycle-latest-title">
                                @if (!empty($card['rejected_url']))
                                    <a href="{{ $card['rejected_url'] }}">{{ $card['rejected_title'] }}</a>
                                @else
                                    {{ $card['rejected_title'] }}
                                @endif
                            </div>
                            @if (!empty($card['rejected_meta']))
                                <div class="cycle-latest-meta">{{ $card['rejected_meta'] }}</div>
                            @endif
                        </div>
                        <div class="cycle-card-footer cycle-card-footer-end mt-3">
                            <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="dashboard-cycle-section mb-4" id="adminApprovalCycles">
        <div class="cycle-section-header mb-3">
            <h5 class="mb-1 fw-bold">{{ $dashboardCycles['adminApprovals']['title'] }}</h5>
            <p class="mb-0 text-muted small">{{ $dashboardCycles['adminApprovals']['subtitle'] }}</p>
        </div>
        <div class="row g-3">
            @foreach ($dashboardCycles['adminApprovals']['cards'] as $card)
                <div class="col-xl-3 col-md-6">
                    <div class="cycle-card h-100">
                        <div class="cycle-card-header">
                            <div class="cycle-card-heading">
                                <div class="cycle-card-title">{{ $card['title'] }}</div>
                                <div class="cycle-card-count">{{ number_format($card['count']) }}</div>
                            </div>
                            <span class="cycle-badge cycle-badge-primary">{{ $card['latest_status'] }}</span>
                        </div>
                        <div class="cycle-latest">
                            <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] }}</div>
                            <div class="cycle-latest-title">
                                @if (!empty($card['latest_url']))
                                    <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] }}</a>
                                @else
                                    {{ $card['latest_title'] }}
                                @endif
                            </div>
                            @if (!empty($card['latest_meta']))
                                <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                            @endif
                        </div>
                        <div class="cycle-card-footer cycle-card-footer-end mt-3">
                            <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="dashboard-cycle-section mb-4" id="pendingCycles">
        <div class="cycle-section-header mb-3">
            <h5 class="mb-1 fw-bold">{{ $dashboardCycles['pending']['title'] }}</h5>
            <p class="mb-0 text-muted small">{{ $dashboardCycles['pending']['subtitle'] }}</p>
        </div>
        <div class="row g-3">
            @foreach ($dashboardCycles['pending']['cards'] as $card)
                <div class="col-xl-3 col-md-6">
                    <div class="cycle-card h-100">
                        <div class="cycle-card-header">
                            <div class="cycle-card-heading">
                                <div class="cycle-card-title">{{ $card['title'] }}</div>
                                <div class="cycle-card-count">{{ number_format($card['count']) }}</div>
                            </div>
                            <span class="cycle-badge cycle-badge-warning">{{ $card['latest_status'] }}</span>
                        </div>
                        <div class="cycle-latest">
                            <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] }}</div>
                            <div class="cycle-latest-title">
                                @if (!empty($card['latest_url']))
                                    <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] }}</a>
                                @else
                                    {{ $card['latest_title'] }}
                                @endif
                            </div>
                            @if (!empty($card['latest_meta']))
                                <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                            @endif
                        </div>
                        <div class="cycle-card-footer cycle-card-footer-end mt-3">
                            <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="dashboard-cycle-section mb-4" id="complaintsCycles">
        <div class="cycle-section-header mb-3">
            <h5 class="mb-1 fw-bold">{{ $dashboardCycles['complaints']['title'] }}</h5>
            <p class="mb-0 text-muted small">{{ $dashboardCycles['complaints']['subtitle'] }}</p>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center text-muted">
                <i class="fas fa-comment-dots fa-2x mb-3"></i>
                <p class="mb-0">{{ $cycleUiLabels['no_complaints_source'] }}</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style data-page-style="admin-dashboard">
        /* Statistics Cards */
        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            overflow: hidden;
            position: relative;
            isolation: isolate;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.14) !important;
            border-color: rgba(255, 255, 255, 0.28);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0));
            pointer-events: none;
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
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.88;
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .stat-subtext {
            font-size: 0.78rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .stat-icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .stat-icon {
            font-size: 1.65rem;
            opacity: 0.95;
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

        .dashboard-cycle-section {
            scroll-margin-top: 110px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 1.25rem;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.06);
            position: relative;
            overflow: hidden;
        }

        .dashboard-cycle-section::after {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, #1d4ed8 0%, #0ea5e9 45%, #22c55e 100%);
            opacity: 0.95;
        }

        .cycle-section-header {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            padding: 0.1rem 0.25rem 0.85rem;
            border-bottom: 1px solid #eef2f7;
            margin-bottom: 1rem !important;
        }

        .cycle-section-header h5 {
            font-size: 1.16rem;
            line-height: 1.35;
            color: #0f172a;
            font-weight: 800 !important;
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cycle-section-header h5::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #22c55e);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            flex: 0 0 10px;
        }

        .cycle-section-header p {
            color: #64748b !important;
            font-size: 0.92rem !important;
            line-height: 1.5;
            margin: 0;
        }

        .cycle-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 1.35rem;
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.06);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .cycle-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
            margin-bottom: 0.9rem;
            flex-wrap: wrap;
        }

        .cycle-card-heading {
            flex: 1 1 180px;
            min-width: 0;
        }

        .cycle-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: auto !important;
            padding-top: 0.15rem;
        }

        .cycle-card-footer-end {
            justify-content: flex-end;
        }

        .cycle-status-text {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .cycle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 34px rgba(15, 23, 42, 0.12);
            border-color: #c7d2fe;
        }

        .cycle-card::before {
            content: '';
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #38bdf8 50%, #22c55e 100%);
        }

        .cycle-card-title {
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 0.4rem;
            word-break: break-word;
            line-height: 1.5;
            text-align: start;
        }

        .cycle-card-count {
            font-size: 2rem;
            line-height: 1;
            font-weight: 900;
            color: #0f172a;
        }

        .cycle-badge {
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.38rem 0.75rem;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            flex: 0 0 auto;
        }

        .cycle-badge-warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .cycle-badge-success {
            background: #ecfdf5;
            color: #047857;
        }

        .cycle-badge-primary {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .cycle-latest {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 0.95rem 1rem;
            margin-top: 1rem;
        }

        .cycle-latest-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 0.25rem;
            font-weight: 700;
            text-align: start;
        }

        .cycle-latest-title a,
        .cycle-latest-title {
            color: #0f172a;
            font-weight: 800;
            text-decoration: none;
            word-break: break-word;
            line-height: 1.4;
            display: block;
            text-align: start;
        }

        .cycle-latest-title a:hover {
            color: #2563eb;
        }

        .cycle-latest-meta {
            color: #64748b;
            font-size: 0.82rem;
            margin-top: 0.25rem;
            text-align: start;
        }

        .cycle-mini-stat {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 0.34rem 0.7rem;
            color: #475569;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .cycle-mini-stat strong {
            color: #0f172a;
        }

        :dir(rtl) .cycle-section-header,
        :dir(rtl) .cycle-card,
        :dir(rtl) .cycle-latest {
            direction: rtl;
        }

        :dir(ltr) .cycle-section-header,
        :dir(ltr) .cycle-card,
        :dir(ltr) .cycle-latest {
            direction: ltr;
        }

        :dir(rtl) .cycle-card-footer-end {
            justify-content: flex-start;
        }

        :dir(ltr) .cycle-card-footer-end {
            justify-content: flex-end;
        }

        .cycle-card .d-flex.flex-wrap.gap-2.mt-2 {
            row-gap: 0.5rem;
        }

        :dir(rtl) .cycle-card-title,
        :dir(rtl) .cycle-latest-label {
            text-transform: none;
            letter-spacing: 0;
        }

        :dir(ltr) .cycle-card-title,
        :dir(ltr) .cycle-latest-label {
            text-transform: uppercase;
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
            border-radius: 999px;
            font-weight: 700;
            transition: all 0.2s ease;
            padding-inline: 0.9rem;
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

        /* Empty States */
        .text-center.py-5 {
            padding: 3rem 1rem !important;
        }

        .dashboard-cycle-section + .dashboard-cycle-section {
            margin-top: 1.25rem;
        }

        .dashboard-cycle-section .row.g-3 {
            margin-top: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-value {
                font-size: 1.5rem;
            }

            .cycle-card-count {
                font-size: 1.45rem;
            }

            .dashboard-cycle-section {
                padding: 1rem;
                border-radius: 18px;
            }

            .cycle-section-header h5 {
                font-size: 1.02rem;
                gap: 0.4rem;
            }

            .cycle-section-header p {
                font-size: 0.86rem !important;
            }

            .cycle-card-header {
                flex-direction: column;
            }

            .cycle-badge {
                align-self: flex-start;
            }

            .cycle-card-footer,
            .cycle-card-footer-end {
                justify-content: flex-start;
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
@endsection
