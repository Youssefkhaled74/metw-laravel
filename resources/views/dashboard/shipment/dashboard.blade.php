@extends('layouts.shipment')

@section('title', __('shipment-dashboard.dashboard'))
@section('page-title', __('shipment-dashboard.page_title_dashboard'))

@section('content')
<div class="row g-3 top-kpi-row mb-3">

    {{-- SHIPPING STATS --}}
    @include('dashboard.shipment.partials.shipping-stats')

    {{-- ECOMMERCE TOTAL ORDERS --}}
    <div class="col-xl-3 col-md-6">
        <div class="card top-kpi-card top-kpi-card--info">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <div class="top-kpi-label mb-1">
                            {{ __('shipment-dashboard.ecommerce_total_orders') }}
                        </div>
                        <div class="top-kpi-value mb-0">
                            {{ number_format($stats['ecommerce_total_orders']) }}
                        </div>
                    </div>
                    <div class="top-kpi-icon">
                        <i class="fas fa-shopping-cart stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ECOMMERCE REVENUE --}}
    <div class="col-xl-3 col-md-6">
        <div class="card top-kpi-card top-kpi-card--success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <div class="top-kpi-label mb-1">
                            {{ __('shipment-dashboard.ecommerce_revenue') }}
                        </div>
                        <div class="top-kpi-value mb-0">
                            {{ __('admin-dashboard.EGP') }}
                            {{ number_format($stats['ecommerce_revenue'],2) }}
                        </div>
                    </div>
                    <div class="top-kpi-icon">
                        <i class="fas fa-coins stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .top-kpi-row .card {
        border: 0;
    }

    .top-kpi-card {
        border-radius: 16px;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.14);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .top-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.2);
    }

    .top-kpi-card .card-body {
        padding: 1.1rem 1rem;
    }

    .top-kpi-card--info {
        background: linear-gradient(140deg, #0ea5e9 0%, #2563eb 100%);
    }

    .top-kpi-card--success {
        background: linear-gradient(140deg, #16a34a 0%, #059669 100%);
    }

    .top-kpi-card--primary {
        background: linear-gradient(140deg, #4f46e5 0%, #2563eb 100%);
    }

    .top-kpi-card--warning {
        background: linear-gradient(140deg, #f59e0b 0%, #ea580c 100%);
    }

    .top-kpi-label {
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .3px;
        opacity: .95;
    }

    html[dir="ltr"] .top-kpi-label {
        text-transform: uppercase;
    }

    .top-kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .top-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .top-kpi-icon .stat-icon {
        font-size: 1.35rem;
        opacity: 1;
    }

    .urgent-board {
        --urgent-surface: #ffffff;
        --urgent-ink: #0f172a;
        --urgent-muted: #64748b;
        --urgent-line: #e2e8f0;
        --urgent-soft: linear-gradient(120deg, #eefbf3 0%, #f7fffb 50%, #f3f8ff 100%);
    }

    .urgent-head {
        background: var(--urgent-soft);
        border: 1px solid #d2f4df;
        border-radius: 18px;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
    }

    .urgent-head h4 {
        color: var(--urgent-ink);
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.2px;
    }

    .urgent-head p {
        margin: 0.35rem 0 0;
        color: var(--urgent-muted);
        font-size: 0.92rem;
    }

    .urgent-section {
        margin-bottom: 1.25rem;
    }

    .urgent-section-panel {
        background: #fff;
        border: 1px solid #d8e3ef;
        border-radius: 18px;
        padding: 1rem;
        padding-inline-start: 1.15rem;
        padding-inline-end: 1rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        position: relative;
    }

    html[dir="rtl"] .urgent-section-panel {
        padding-inline-start: 1rem;
        padding-inline-end: 1.15rem;
    }

    .urgent-section-panel::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 18px 0 0 18px;
        background: #cbd5e1;
    }

    html[dir="rtl"] .urgent-section-panel::before {
        inset-inline-start: auto;
        inset-inline-end: 0;
        border-radius: 0 18px 18px 0;
    }

    .urgent-section-panel--express::before {
        background: linear-gradient(180deg, #ef4444 0%, #f97316 100%);
    }

    .urgent-section-panel--metwzon::before {
        background: linear-gradient(180deg, #f59e0b 0%, #eab308 100%);
    }

    .urgent-section-panel--follow::before {
        background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
    }

    .urgent-section + .urgent-section {
        margin-top: 1.2rem;
    }

    .urgent-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-weight: 800;
        color: #0b3c26;
        margin: 0 0 .75rem;
        text-align: start;
    }

    .urgent-task-card {
        background: var(--urgent-surface);
        border: 1px solid var(--urgent-line);
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        height: 100%;
        overflow: hidden;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .urgent-task-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.10);
    }

    .urgent-task-card .task-top {
        padding: .9rem 1rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
    }

    .urgent-task-card .task-title {
        color: var(--urgent-ink);
        font-size: .95rem;
        margin: 0;
        font-weight: 700;
        line-height: 1.35;
        text-align: start;
    }

    .urgent-task-card .task-icon {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }

    .urgent-task-card .task-body {
        padding: .85rem 1rem .95rem;
    }

    .task-counter {
        display: flex;
        align-items: baseline;
        gap: .35rem;
        margin-bottom: .7rem;
    }

    .task-counter .value {
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .task-counter .label {
        color: var(--urgent-muted);
        font-size: .82rem;
    }

    .urgent-feed {
        display: flex;
        flex-direction: column;
        gap: .45rem;
    }

    .urgent-feed-item {
        border: 1px solid #eaf0f6;
        border-radius: 10px;
        padding: .45rem .58rem;
        background: #fbfdff;
        text-align: start;
    }

    .urgent-feed-item a {
        text-decoration: none;
        color: #0f172a;
        font-weight: 700;
        font-size: .88rem;
        unicode-bidi: plaintext;
    }

    .urgent-feed-item .sub {
        display: block;
        color: #64748b;
        font-size: .8rem;
        margin-top: .1rem;
        unicode-bidi: plaintext;
    }

    .urgent-feed-item .meta {
        display: inline-block;
        color: #94a3b8;
        font-size: .74rem;
        margin-top: .15rem;
        unicode-bidi: plaintext;
    }

    .urgent-empty {
        color: #94a3b8;
        font-size: .83rem;
        padding: .55rem 0;
    }

    .urgent-action {
        margin-top: .75rem;
    }

    .urgent-action .btn {
        border-radius: 9px;
    }

    .urgent-accent-danger .task-icon { background: linear-gradient(135deg, #ef4444, #f97316); }
    .urgent-accent-warning .task-icon { background: linear-gradient(135deg, #eab308, #f59e0b); }
    .urgent-accent-info .task-icon { background: linear-gradient(135deg, #06b6d4, #3b82f6); }
    .urgent-accent-success .task-icon { background: linear-gradient(135deg, #16a34a, #10b981); }
    .urgent-accent-primary .task-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .urgent-accent-secondary .task-icon { background: linear-gradient(135deg, #94a3b8, #64748b); }

    @media (max-width: 767.98px) {
        .urgent-head { padding: .85rem 1rem; }
        .urgent-task-card .task-title { font-size: .9rem; }
        .urgent-section-panel { padding: .85rem; }
    }
</style>

<div class="urgent-board">
    <div class="urgent-head">
        <h4>{{ __('shipment-dashboard.urgent_tasks_title') }}</h4>
        <p>{{ __('shipment-dashboard.urgent_tasks_subtitle') }}</p>
    </div>

    <div class="urgent-section">
        <div class="urgent-section-panel urgent-section-panel--express">
            <h5 class="urgent-section-title">
                <i class="fa-solid fa-truck-fast text-danger"></i>
                {{ __('shipment-dashboard.urgent_metw_express_section') }}
            </h5>
            <div class="row g-3">
                @foreach($urgent_tasks['metw_express']['cards'] as $card)
                    <div class="col-xl-4 col-md-6">
                        <div class="urgent-task-card urgent-accent-{{ $card['accent'] }}">
                            <div class="task-top">
                                <h6 class="task-title">{{ $card['title'] }}</h6>
                                <span class="task-icon"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                            </div>
                            <div class="task-body">
                                <div class="task-counter">
                                    <span class="value">{{ number_format($card['count']) }}</span>
                                    <span class="label">{{ __('shipment-dashboard.urgent_items_label') }}</span>
                                </div>

                                @if(collect($card['items'])->count())
                                    <div class="urgent-feed">
                                        @foreach($card['items'] as $item)
                                            <div class="urgent-feed-item">
                                                <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                                <span class="sub">{{ $item['subtitle'] }}</span>
                                                <span class="meta">{{ $item['meta'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="urgent-empty">{{ __('shipment-dashboard.urgent_no_items') }}</div>
                                @endif

                                @if(!empty($card['url']))
                                    <div class="urgent-action">
                                        <a href="{{ $card['url'] }}" class="btn btn-sm btn-outline-dark w-100">{{ __('shipment-dashboard.urgent_open_list') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="urgent-section">
        <div class="urgent-section-panel urgent-section-panel--metwzon">
            <h5 class="urgent-section-title">
                <i class="fa-solid fa-bag-shopping text-warning"></i>
                {{ __('shipment-dashboard.urgent_metwzon_section') }}
            </h5>
            <div class="row g-3">
                @foreach($urgent_tasks['metwzon']['cards'] as $card)
                    <div class="col-xl-4 col-md-6">
                        <div class="urgent-task-card urgent-accent-{{ $card['accent'] }}">
                            <div class="task-top">
                                <h6 class="task-title">{{ $card['title'] }}</h6>
                                <span class="task-icon"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                            </div>
                            <div class="task-body">
                                <div class="task-counter">
                                    <span class="value">{{ number_format($card['count']) }}</span>
                                    <span class="label">{{ __('shipment-dashboard.urgent_items_label') }}</span>
                                </div>

                                @if(collect($card['items'])->count())
                                    <div class="urgent-feed">
                                        @foreach($card['items'] as $item)
                                            <div class="urgent-feed-item">
                                                <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                                <span class="sub">{{ $item['subtitle'] }}</span>
                                                <span class="meta">{{ $item['meta'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="urgent-empty">{{ __('shipment-dashboard.urgent_no_items') }}</div>
                                @endif

                                @if(!empty($card['url']))
                                    <div class="urgent-action">
                                        <a href="{{ $card['url'] }}" class="btn btn-sm btn-outline-dark w-100">{{ __('shipment-dashboard.urgent_open_list') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="urgent-section">
        <div class="urgent-section-panel urgent-section-panel--follow">
            <h5 class="urgent-section-title">
                <i class="fa-solid fa-wave-square text-primary"></i>
                {{ __('shipment-dashboard.urgent_follow_up_section') }}
            </h5>
            <div class="row g-3">
                @foreach($urgent_tasks['follow_up']['cards'] as $card)
                    <div class="col-xl-4 col-md-6">
                        <div class="urgent-task-card urgent-accent-{{ $card['accent'] }}">
                            <div class="task-top">
                                <h6 class="task-title">{{ $card['title'] }}</h6>
                                <span class="task-icon"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                            </div>
                            <div class="task-body">
                                <div class="task-counter">
                                    <span class="value">{{ number_format($card['count']) }}</span>
                                    <span class="label">{{ __('shipment-dashboard.urgent_items_label') }}</span>
                                </div>

                                @if(collect($card['items'])->count())
                                    <div class="urgent-feed">
                                        @foreach($card['items'] as $item)
                                            <div class="urgent-feed-item">
                                                <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                                <span class="sub">{{ $item['subtitle'] }}</span>
                                                <span class="meta">{{ $item['meta'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="urgent-empty">{{ __('shipment-dashboard.urgent_no_items') }}</div>
                                @endif

                                @if(!empty($card['url']))
                                    <div class="urgent-action">
                                        <a href="{{ $card['url'] }}" class="btn btn-sm btn-outline-dark w-100">{{ __('shipment-dashboard.urgent_open_list') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
