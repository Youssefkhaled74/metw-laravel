@extends('layouts.admin')

@section('title', __('admin-dashboard.monthly_revenue'))
@section('page-title', __('admin-dashboard.monthly_revenue'))

@section('content')
    @php
        $locale = app()->getLocale();
        $isArabic = $locale === 'ar';

        $shipmentSeries = collect($monthly_revenue['shipment'] ?? []);
        $ecommerceSeries = collect($monthly_revenue['ecommerce'] ?? []);

        $shipmentTotal = $shipmentSeries->sum(fn ($item) => (float) ($item->total ?? 0));
        $ecommerceTotal = $ecommerceSeries->sum(fn ($item) => (float) ($item->total ?? 0));
        $overallTotal = $shipmentTotal + $ecommerceTotal;

        $shipmentPercent = $overallTotal > 0 ? round(($shipmentTotal / $overallTotal) * 100) : 0;
        $ecommercePercent = $overallTotal > 0 ? round(($ecommerceTotal / $overallTotal) * 100) : 0;

        $allRows = $shipmentSeries
            ->merge($ecommerceSeries)
            ->filter(fn ($item) => isset($item->year, $item->month));

        $activeMonths = $allRows
            ->map(fn ($item) => ((int) $item->year) . '-' . str_pad((string) ((int) $item->month), 2, '0', STR_PAD_LEFT))
            ->unique()
            ->count();

        $averageMonthly = $activeMonths > 0 ? $overallTotal / $activeMonths : 0;

        $topShipment = $shipmentSeries->sortByDesc(fn ($item) => (float) ($item->total ?? 0))->first();
        $topEcommerce = $ecommerceSeries->sortByDesc(fn ($item) => (float) ($item->total ?? 0))->first();

        $formatMonth = function ($item) {
            if (!$item || !isset($item->year, $item->month)) {
                return '--';
            }

            return str_pad((string) ((int) $item->month), 2, '0', STR_PAD_LEFT) . '/' . (int) $item->year;
        };

        $shipmentChartData = $shipmentSeries
            ->map(fn ($data) => [
                'year' => (int) $data->year,
                'month' => (int) $data->month,
                'total' => (float) ($data->total ?? 0),
            ])
            ->values();

        $ecommerceChartData = $ecommerceSeries
            ->map(fn ($data) => [
                'year' => (int) $data->year,
                'month' => (int) $data->month,
                'total' => (float) ($data->total ?? 0),
            ])
            ->values();

        $jsLabels = [
            'shipment' => __('admin-dashboard.shipment_revenue'),
            'ecommerce' => __('admin-dashboard.ecommerce_revenue'),
            'monthlyRevenue' => __('admin-dashboard.monthly_revenue'),
        ];
    @endphp

    <div class="mrev-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="mrev-hero">
            <div class="mrev-hero-main">
                <div class="mrev-hero-icon">
                    <i class="fas fa-chart-line"></i>
                </div>

                <div class="mrev-hero-copy">
                    <span class="mrev-chip">
                        {{ $isArabic ? 'تقارير الإيرادات' : 'Revenue analytics' }}
                    </span>

                    <h3>{{ __('admin-dashboard.monthly_revenue') }}</h3>

                    <p>
                        {{ $isArabic
                            ? 'متابعة أداء الإيرادات الشهرية للشحن وتطبيق الماركت مع مقارنة إجمالي كل مصدر.'
                            : 'Track monthly revenue performance for shipment and ecommerce streams with a clean source comparison.'
                        }}
                    </p>
                </div>
            </div>

            <div class="mrev-total-card">
                <span>{{ __('admin-dashboard.total_amount') }}</span>
                <strong>{{ number_format($overallTotal, 2) }}</strong>
                <small>
                    {{ $isArabic ? 'إجمالي الإيرادات المعروضة' : 'Total displayed revenue' }}
                </small>
            </div>
        </section>

        <section class="mrev-kpis">
            <div class="mrev-kpi">
                <div class="mrev-kpi-icon primary">
                    <i class="fas fa-truck"></i>
                </div>

                <div>
                    <span>{{ __('admin-dashboard.shipment_revenue') }}</span>
                    <strong>{{ number_format($shipmentTotal, 2) }}</strong>
                    <small>{{ $shipmentPercent }}% {{ $isArabic ? 'من الإجمالي' : 'of total' }}</small>
                </div>
            </div>

            <div class="mrev-kpi">
                <div class="mrev-kpi-icon success">
                    <i class="fas fa-store"></i>
                </div>

                <div>
                    <span>{{ __('admin-dashboard.ecommerce_revenue') }}</span>
                    <strong>{{ number_format($ecommerceTotal, 2) }}</strong>
                    <small>{{ $ecommercePercent }}% {{ $isArabic ? 'من الإجمالي' : 'of total' }}</small>
                </div>
            </div>

            <div class="mrev-kpi">
                <div class="mrev-kpi-icon purple">
                    <i class="fas fa-calendar-days"></i>
                </div>

                <div>
                    <span>{{ $isArabic ? 'الشهور النشطة' : 'Active months' }}</span>
                    <strong>{{ number_format($activeMonths) }}</strong>
                    <small>{{ $isArabic ? 'شهور بها بيانات' : 'months with data' }}</small>
                </div>
            </div>

            <div class="mrev-kpi">
                <div class="mrev-kpi-icon warning">
                    <i class="fas fa-gauge-high"></i>
                </div>

                <div>
                    <span>{{ $isArabic ? 'متوسط شهري' : 'Monthly average' }}</span>
                    <strong>{{ number_format($averageMonthly, 2) }}</strong>
                    <small>{{ $isArabic ? 'حسب البيانات الحالية' : 'based on current data' }}</small>
                </div>
            </div>
        </section>

        <section class="mrev-breakdown">
            <div class="mrev-breakdown-card">
                <div class="mrev-breakdown-head">
                    <div>
                        <h5>{{ $isArabic ? 'توزيع الإيرادات' : 'Revenue split' }}</h5>
                        <p>{{ $isArabic ? 'مقارنة مباشرة بين إيرادات الشحن وتطبيق الماركت.' : 'A direct comparison between shipment and ecommerce revenue.' }}</p>
                    </div>
                </div>

                <div class="mrev-split">
                    <div class="mrev-split-row">
                        <div>
                            <span>{{ __('admin-dashboard.shipment_revenue') }}</span>
                            <strong>{{ number_format($shipmentTotal, 2) }}</strong>
                        </div>

                        <em>{{ $shipmentPercent }}%</em>
                    </div>

                    <div class="mrev-split-bar">
                        <span class="shipment" style="width: {{ $shipmentPercent }}%"></span>
                    </div>

                    <div class="mrev-split-row mt-3">
                        <div>
                            <span>{{ __('admin-dashboard.ecommerce_revenue') }}</span>
                            <strong>{{ number_format($ecommerceTotal, 2) }}</strong>
                        </div>

                        <em>{{ $ecommercePercent }}%</em>
                    </div>

                    <div class="mrev-split-bar">
                        <span class="ecommerce" style="width: {{ $ecommercePercent }}%"></span>
                    </div>
                </div>
            </div>

            <div class="mrev-breakdown-card">
                <div class="mrev-breakdown-head">
                    <div>
                        <h5>{{ $isArabic ? 'أفضل شهر' : 'Top month' }}</h5>
                        <p>{{ $isArabic ? 'أعلى شهر مسجل لكل مصدر إيراد.' : 'Highest recorded month for each revenue stream.' }}</p>
                    </div>
                </div>

                <div class="mrev-top-list">
                    <div>
                        <span>{{ __('admin-dashboard.shipment_revenue') }}</span>
                        <strong>{{ number_format((float) ($topShipment->total ?? 0), 2) }}</strong>
                        <small>{{ $formatMonth($topShipment) }}</small>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.ecommerce_revenue') }}</span>
                        <strong>{{ number_format((float) ($topEcommerce->total ?? 0), 2) }}</strong>
                        <small>{{ $formatMonth($topEcommerce) }}</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="mrev-chart-card">
            <div class="mrev-chart-head">
                <div>
                    <h5>
                        <i class="fas fa-chart-line {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.monthly_revenue') }}
                    </h5>

                    <p>
                        {{ $isArabic
                            ? 'استخدم الفلاتر لاختيار سنة أو شهر محدد داخل الرسم البياني.'
                            : 'Use filters to focus the chart on a specific year or month.'
                        }}
                    </p>
                </div>

                <div class="mrev-filters">
                    <div class="mrev-filter-item">
                        <label for="revenueYearFilter">{{ $isArabic ? 'السنة' : 'Year' }}</label>
                        <select id="revenueYearFilter" class="form-select form-select-sm"></select>
                    </div>

                    <div class="mrev-filter-item">
                        <label for="revenueMonthFilter">{{ $isArabic ? 'الشهر' : 'Month' }}</label>
                        <select id="revenueMonthFilter" class="form-select form-select-sm"></select>
                    </div>
                </div>
            </div>

            <div class="mrev-chart-body">
                @if($overallTotal > 0)
                    <div class="mrev-chart-area">
                        <canvas
                            id="monthlyRevenueChart"
                            data-shipment-series='@json($shipmentChartData)'
                            data-ecommerce-series='@json($ecommerceChartData)'
                            data-shipment-label="{{ __('admin-dashboard.shipment_revenue') }}"
                            data-ecommerce-label="{{ __('admin-dashboard.ecommerce_revenue') }}"
                        ></canvas>
                    </div>
                @else
                    <div class="mrev-empty-chart">
                        <i class="fas fa-chart-simple"></i>
                        <h5>{{ $isArabic ? 'لا توجد إيرادات بعد' : 'No revenue yet' }}</h5>
                        <p>{{ $isArabic ? 'سيظهر الرسم البياني هنا عند توفر بيانات الإيرادات الشهرية.' : 'The chart will appear here when monthly revenue data is available.' }}</p>

                        <canvas
                            id="monthlyRevenueChart"
                            class="d-none"
                            data-shipment-series='@json($shipmentChartData)'
                            data-ecommerce-series='@json($ecommerceChartData)'
                            data-shipment-label="{{ __('admin-dashboard.shipment_revenue') }}"
                            data-ecommerce-label="{{ __('admin-dashboard.ecommerce_revenue') }}"
                        ></canvas>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <style data-page-style="admin-monthly-revenue">
        .mrev-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mrev-hero,
        .mrev-kpi,
        .mrev-breakdown-card,
        .mrev-chart-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .mrev-hero {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .mrev-hero::before {
            content: "";
            position: absolute;
            inset-inline-start: -90px;
            top: -90px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(37, 99, 235, .08);
            pointer-events: none;
        }

        .mrev-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .mrev-hero-icon {
            width: 66px;
            height: 66px;
            border-radius: 24px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
        }

        .mrev-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .75rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 900;
        }

        .mrev-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .mrev-hero-copy p {
            max-width: 850px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .mrev-total-card {
            min-width: 260px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .mrev-total-card span,
        .mrev-kpi span,
        .mrev-split-row span,
        .mrev-top-list span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .mrev-total-card strong {
            display: block;
            color: #111827;
            font-size: 1.55rem;
            font-weight: 950;
            line-height: 1;
        }

        .mrev-total-card small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            margin-top: .5rem;
        }

        .mrev-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .mrev-kpi {
            min-height: 108px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: .8rem;
        }

        .mrev-kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .mrev-kpi-icon.primary {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
        }

        .mrev-kpi-icon.success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .mrev-kpi-icon.purple {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            color: #7c3aed;
        }

        .mrev-kpi-icon.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .mrev-kpi strong {
            display: block;
            color: #111827;
            font-size: 1.28rem;
            font-weight: 950;
            line-height: 1.25;
            word-break: break-word;
        }

        .mrev-kpi small {
            display: block;
            color: #64748b;
            font-size: .76rem;
            margin-top: .45rem;
            line-height: 1.5;
        }

        .mrev-breakdown {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr);
            gap: .75rem;
        }

        .mrev-breakdown-card {
            padding: 1.25rem;
        }

        .mrev-breakdown-head {
            margin-bottom: 1rem;
        }

        .mrev-breakdown-head h5,
        .mrev-chart-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .mrev-breakdown-head p,
        .mrev-chart-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.7;
        }

        .mrev-split-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-end;
        }

        .mrev-split-row strong {
            display: block;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .mrev-split-row em {
            font-style: normal;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
        }

        .mrev-split-bar {
            height: 10px;
            margin-top: .6rem;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .mrev-split-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
        }

        .mrev-split-bar span.shipment {
            background: #2563eb;
        }

        .mrev-split-bar span.ecommerce {
            background: #047857;
        }

        .mrev-top-list {
            display: grid;
            gap: .75rem;
        }

        .mrev-top-list div {
            padding: .9rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .mrev-top-list strong {
            display: block;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .mrev-top-list small {
            display: block;
            color: #64748b;
            margin-top: .25rem;
            font-size: .78rem;
            font-weight: 850;
        }

        .mrev-chart-card {
            overflow: hidden;
        }

        .mrev-chart-head {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .mrev-filters {
            display: flex;
            align-items: end;
            gap: .7rem;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .mrev-filter-item {
            min-width: 132px;
        }

        .mrev-filter-item label {
            display: block;
            color: #64748b;
            font-size: .74rem;
            font-weight: 950;
            margin-bottom: .3rem;
        }

        .mrev-filter-item .form-select {
            min-height: 38px;
            border-radius: 12px;
            border-color: #dbe3ea;
            color: #111827;
            font-size: .82rem;
            font-weight: 850;
            box-shadow: none;
        }

        .mrev-filter-item .form-select:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .mrev-chart-body {
            background: #fff;
        }

        .mrev-chart-area {
            position: relative;
            height: 430px;
            padding: 1.25rem;
        }

        .mrev-empty-chart {
            min-height: 360px;
            padding: 2rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: #fbfdff;
        }

        .mrev-empty-chart i {
            width: 78px;
            height: 78px;
            border-radius: 26px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .mrev-empty-chart h5 {
            color: #111827;
            font-weight: 950;
            margin-bottom: .45rem;
        }

        .mrev-empty-chart p {
            max-width: 560px;
            margin: 0;
            color: #64748b;
            line-height: 1.8;
        }

        @media (max-width: 1199.98px) {
            .mrev-hero,
            .mrev-chart-head {
                flex-direction: column;
                align-items: stretch;
            }

            .mrev-total-card {
                min-width: 0;
            }

            .mrev-kpis {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mrev-breakdown {
                grid-template-columns: 1fr;
            }

            .mrev-filters {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .mrev-hero,
            .mrev-kpi,
            .mrev-breakdown-card,
            .mrev-chart-card {
                border-radius: 18px;
            }

            .mrev-hero,
            .mrev-breakdown-card,
            .mrev-chart-head {
                padding: 1rem;
            }

            .mrev-hero-main {
                flex-direction: column;
            }

            .mrev-hero-copy h3 {
                font-size: 1.2rem;
            }

            .mrev-kpis {
                grid-template-columns: 1fr;
            }

            .mrev-filter-item {
                flex: 1 1 130px;
            }

            .mrev-chart-area {
                height: 330px;
                padding: .75rem;
            }
        }
    </style>

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
@endsection