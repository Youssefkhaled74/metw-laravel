@extends('layouts.admin')

@section('title', 'المهام العاجلة')
@section('page-title', 'المهام العاجلة')

@push('styles')
    <style>
        .urgent-page {
            direction: rtl;
        }

        .urgent-hero {
            background: linear-gradient(135deg, #fff7ed 0%, #f5f3ff 100%);
            border: 1px solid rgba(249, 115, 22, 0.15);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
        }

        .urgent-total-box {
            min-width: 160px;
            text-align: center;
            background: #ffffff;
            border: 1px solid rgba(168, 85, 247, 0.14);
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.04);
        }

        .urgent-total-box strong {
            display: block;
            font-size: 1.75rem;
            line-height: 1;
            color: #7c3aed;
        }

        .urgent-section-card {
            border: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 0.5rem 1.5rem rgba(15, 23, 42, 0.05);
        }

        .urgent-section-head {
            background: linear-gradient(90deg, rgba(249, 115, 22, 0.08) 0%, rgba(124, 58, 237, 0.06) 100%);
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .urgent-row {
            border: 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .urgent-row:hover {
            background: #fffaf4;
            transform: translateY(-1px);
        }

        .urgent-row:last-child {
            border-bottom: 0;
        }

        .urgent-row .urgent-label {
            color: #111827;
            font-weight: 700;
        }

        .urgent-row .urgent-note {
            color: #64748b;
            font-size: 0.92rem;
        }

        .urgent-count {
            min-width: 3.75rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            text-align: center;
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.18);
        }

        .urgent-link {
            color: inherit;
            text-decoration: none;
        }

        .urgent-link:hover {
            color: inherit;
        }

        .urgent-link .urgent-chevron {
            color: #fb923c;
        }
    </style>
@endpush

@section('content')
    @php
        $visibleItems = collect($sections)->pluck('items')->flatten(1)->count();
    @endphp

    <div class="urgent-page">
        <section class="urgent-hero mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge rounded-pill text-bg-warning text-dark mb-2">لوحة المتابعة العاجلة</span>
                    <h3 class="mb-2">المهام العاجلة</h3>
                    <p class="text-muted mb-0">
                        متابعة سريعة للحسابات والطلبات والموافقات والشكاوى التي تحتاج إجراءً الآن.
                    </p>
                </div>

                <div class="urgent-total-box">
                    <span class="text-muted d-block mb-1">إجمالي العناصر الظاهرة</span>
                    <strong>{{ number_format($totalUrgentItems) }}</strong>
                    <small class="text-muted">{{ number_format($visibleItems) }} عنصر داخل الأقسام</small>
                </div>
            </div>
        </section>

        <div class="row g-4">
            @foreach ($sections as $section)
                <div class="col-12">
                    <section class="card urgent-section-card">
                        <div class="card-header urgent-section-head py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="mb-1 fw-bold">{{ $section['title'] }}</h5>
                                    <p class="mb-0 text-muted small">
                                        {{ number_format(collect($section['items'])->sum('count')) }} عنصر يحتاج متابعة
                                    </p>
                                </div>
                                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                                    <i class="fas fa-list me-1"></i>
                                    {{ count($section['items']) }} بنود
                                </span>
                            </div>
                        </div>

                        <div class="list-group list-group-flush">
                            @foreach ($section['items'] as $item)
                                @if (!empty($item['url']))
                                    <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action urgent-row urgent-link">
                                @else
                                    <div class="list-group-item urgent-row">
                                @endif
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div class="flex-grow-1">
                                                <div class="urgent-label mb-1">{{ $item['label'] }}</div>
                                                <div class="urgent-note">{{ $item['note'] }}</div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <span class="urgent-count">{{ number_format($item['count']) }}</span>
                                                @if (!empty($item['url']))
                                                    <i class="fas fa-chevron-left urgent-chevron"></i>
                                                @endif
                                            </div>
                                        </div>
                                @if (!empty($item['url']))
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>
                </div>
            @endforeach
        </div>
    </div>
@endsection
