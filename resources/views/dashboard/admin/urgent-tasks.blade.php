@extends('layouts.admin')

@section('title', 'نظرة عامة على لوحة التحكم')
@section('page-title', 'نظرة عامة على لوحة التحكم')

@section('content')
    @php
        $sectionsCollection = collect($sections ?? []);
        $visibleItems = $sectionsCollection->pluck('items')->flatten(1)->count();
        $sectionsCount = $sectionsCollection->count();
        $activeSectionsCount = $sectionsCollection->filter(fn ($section) => collect($section['items'] ?? [])->sum('count') > 0)->count();

        $topItem = $sectionsCollection
            ->pluck('items')
            ->flatten(1)
            ->sortByDesc('count')
            ->first();

        $topItemLabel = data_get($topItem, 'label', 'لا توجد عناصر');
        $topItemCount = (int) data_get($topItem, 'count', 0);

        $sectionIcon = function ($title) {
            $title = (string) $title;

            if (str_contains($title, 'مندوب') || str_contains($title, 'مناديب')) {
                return 'fa-user-tie';
            }

            if (str_contains($title, 'طلب') || str_contains($title, 'طلبات')) {
                return 'fa-box';
            }

            if (str_contains($title, 'شكوى') || str_contains($title, 'شكاوى')) {
                return 'fa-triangle-exclamation';
            }

            if (str_contains($title, 'موافقة') || str_contains($title, 'مراجعة')) {
                return 'fa-clipboard-check';
            }

            if (str_contains($title, 'مستند') || str_contains($title, 'ملف')) {
                return 'fa-file-lines';
            }

            return 'fa-bolt';
        };

        $itemTone = function ($count) {
            $count = (int) $count;

            if ($count >= 50) {
                return 'danger';
            }

            if ($count >= 10) {
                return 'warning';
            }

            if ($count > 0) {
                return 'active';
            }

            return 'empty';
        };

        $jsLabels = [
            'visible' => 'ظاهر',
            'noMatches' => 'لا توجد نتائج مطابقة',
        ];
    @endphp

    <div class="urg-page" dir="rtl">
        <section class="urg-hero">
            <div class="urg-hero-main">
                <div class="urg-hero-icon">
                    <i class="fas fa-bolt"></i>
                </div>

                <div class="urg-hero-copy">
                    <span class="urg-chip">لوحة المتابعة العاجلة</span>

                    <h3>نظرة عامة على لوحة التحكم</h3>

                    <p>
                        متابعة سريعة للحسابات والطلبات والموافقات والشكاوى التي تحتاج إجراءً فوريًا، مع عرض الأولويات بشكل واضح ومنظم.
                    </p>
                </div>
            </div>

            <div class="urg-priority-card">
                <span>أعلى أولوية</span>
                <strong>{{ $topItemLabel }}</strong>
                <small>{{ number_format($topItemCount) }} عنصر يحتاج متابعة</small>
            </div>
        </section>

        <section class="urg-metrics">
            <div class="urg-metric">
                <div class="urg-metric-icon danger">
                    <i class="fas fa-fire"></i>
                </div>

                <div>
                    <span>إجمالي العناصر العاجلة</span>
                    <strong>{{ number_format($totalUrgentItems) }}</strong>
                    <small>كل العناصر التي تحتاج إجراء.</small>
                </div>
            </div>

            <div class="urg-metric">
                <div class="urg-metric-icon warning">
                    <i class="fas fa-layer-group"></i>
                </div>

                <div>
                    <span>الأقسام</span>
                    <strong>{{ number_format($sectionsCount) }}</strong>
                    <small>{{ number_format($activeSectionsCount) }} قسم به عناصر نشطة.</small>
                </div>
            </div>

            <div class="urg-metric">
                <div class="urg-metric-icon info">
                    <i class="fas fa-list-check"></i>
                </div>

                <div>
                    <span>البنود الظاهرة</span>
                    <strong>{{ number_format($visibleItems) }}</strong>
                    <small>بنود داخل الأقسام الحالية.</small>
                </div>
            </div>

            <div class="urg-metric">
                <div class="urg-metric-icon success">
                    <i class="fas fa-gauge-high"></i>
                </div>

                <div>
                    <span>حالة المتابعة</span>
                    <strong>{{ $totalUrgentItems > 0 ? 'نشطة' : 'هادئة' }}</strong>
                    <small>{{ $totalUrgentItems > 0 ? 'يوجد عناصر تحتاج تدخل.' : 'لا توجد عناصر عاجلة الآن.' }}</small>
                </div>
            </div>
        </section>

        <section class="urg-toolbar">
            <div class="urg-search">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="urgentSearchInput"
                    placeholder="بحث سريع داخل نظرة عامة على لوحة التحكم..."
                >
            </div>

            <div class="urg-filters">
                <button type="button" class="urg-filter active" data-filter="all">الكل</button>
                <button type="button" class="urg-filter" data-filter="active">بها عناصر</button>
                <button type="button" class="urg-filter" data-filter="danger">أولوية عالية</button>
                <button type="button" class="urg-filter" data-filter="empty">بدون عناصر</button>
            </div>

            <div class="urg-counter" id="urgentVisibleCounter">
                {{ number_format($visibleItems) }} بند
            </div>
        </section>

        @if($sectionsCollection->count() > 0)
            <div class="urg-sections" id="urgentSections">
                @foreach ($sectionsCollection as $sectionIndex => $section)
                    @php
                        $items = collect($section['items'] ?? []);
                        $sectionTotal = $items->sum('count');
                        $sectionTone = $itemTone($sectionTotal);
                        $icon = $sectionIcon($section['title'] ?? '');
                    @endphp

                    <section
                        class="urg-section-card urg-searchable-section"
                        data-section-total="{{ $sectionTotal }}"
                        data-tone="{{ $sectionTone }}"
                        data-search="{{ strtolower(($section['title'] ?? '') . ' ' . $items->pluck('label')->implode(' ') . ' ' . $items->pluck('note')->implode(' ')) }}"
                    >
                        <div class="urg-section-head">
                            <div class="urg-section-title">
                                <div class="urg-section-icon {{ $sectionTone }}">
                                    <i class="fas {{ $icon }}"></i>
                                </div>

                                <div>
                                    <h5>{{ $section['title'] }}</h5>
                                    <p>{{ number_format($sectionTotal) }} عنصر يحتاج متابعة داخل هذا القسم</p>
                                </div>
                            </div>

                            <div class="urg-section-meta">
                                <span class="urg-section-count {{ $sectionTone }}">
                                    {{ number_format($sectionTotal) }}
                                </span>

                                <span class="urg-section-items">
                                    <i class="fas fa-list ms-1"></i>
                                    {{ $items->count() }} بنود
                                </span>
                            </div>
                        </div>

                        <div class="urg-items">
                            @forelse ($items as $item)
                                @php
                                    $count = (int) ($item['count'] ?? 0);
                                    $tone = $itemTone($count);
                                    $hasUrl = !empty($item['url']);
                                    $searchText = strtolower(($item['label'] ?? '') . ' ' . ($item['note'] ?? '') . ' ' . $count);
                                @endphp

                                @if ($hasUrl)
                                    <a
                                        href="{{ $item['url'] }}"
                                        class="urg-item urg-searchable-item"
                                        data-count="{{ $count }}"
                                        data-tone="{{ $tone }}"
                                        data-search="{{ $searchText }}"
                                    >
                                @else
                                    <div
                                        class="urg-item urg-searchable-item"
                                        data-count="{{ $count }}"
                                        data-tone="{{ $tone }}"
                                        data-search="{{ $searchText }}"
                                    >
                                @endif
                                        <div class="urg-item-main">
                                            <div class="urg-item-status {{ $tone }}">
                                                <i class="fas {{ $hasUrl ? 'fa-arrow-up-right-from-square' : 'fa-circle-info' }}"></i>
                                            </div>

                                            <div>
                                                <h6>{{ $item['label'] }}</h6>
                                                <p>{{ $item['note'] }}</p>
                                            </div>
                                        </div>

                                        <div class="urg-item-side">
                                            <span class="urg-count {{ $tone }}">
                                                {{ number_format($count) }}
                                            </span>

                                            @if ($hasUrl)
                                                <i class="fas fa-chevron-left urg-chevron"></i>
                                            @endif
                                        </div>
                                @if ($hasUrl)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @empty
                                <div class="urg-empty-inline">
                                    <i class="fas fa-circle-check"></i>
                                    <strong>لا توجد بنود داخل هذا القسم</strong>
                                    <span>هذا القسم لا يحتوي على عناصر عاجلة حاليًا.</span>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="urg-empty-filter d-none" id="urgentEmptyFilter">
                <i class="fas fa-search"></i>
                <h5>لا توجد نتائج مطابقة</h5>
                <p>جرّب تغيير كلمة البحث أو الفلتر المحدد.</p>
            </div>
        @else
            <section class="urg-empty">
                <div class="urg-empty-icon">
                    <i class="fas fa-circle-check"></i>
                </div>

                <h5>لا توجد مهام عاجلة</h5>
                <p>لا توجد أقسام أو عناصر تحتاج متابعة في الوقت الحالي.</p>
            </section>
        @endif
    </div>

    <style data-page-style>
        .urg-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .urg-hero,
        .urg-metric,
        .urg-toolbar,
        .urg-section-card,
        .urg-empty,
        .urg-empty-filter {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .urg-hero {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .urg-hero::before {
            content: "";
            position: absolute;
            inset-inline-start: -80px;
            top: -90px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(245, 158, 11, .08);
            pointer-events: none;
        }

        .urg-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            position: relative;
            z-index: 1;
            min-width: 0;
        }

        .urg-hero-icon {
            width: 66px;
            height: 66px;
            border-radius: 24px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #ea580c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
        }

        .urg-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .75rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: .78rem;
            font-weight: 900;
        }

        .urg-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .urg-hero-copy p {
            max-width: 850px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .urg-priority-card {
            min-width: 270px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .urg-priority-card span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .urg-priority-card strong {
            display: block;
            color: #111827;
            font-size: .95rem;
            font-weight: 950;
            line-height: 1.6;
        }

        .urg-priority-card small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            margin-top: .35rem;
        }

        .urg-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .urg-metric {
            padding: 1rem;
            min-height: 106px;
            display: flex;
            align-items: flex-start;
            gap: .8rem;
        }

        .urg-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
        }

        .urg-metric-icon.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .urg-metric-icon.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .urg-metric-icon.info {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .urg-metric-icon.success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .urg-metric span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .urg-metric strong {
            display: block;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            line-height: 1.1;
        }

        .urg-metric small {
            display: block;
            color: #64748b;
            font-size: .76rem;
            margin-top: .45rem;
            line-height: 1.5;
        }

        .urg-toolbar {
            padding: .9rem;
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto auto;
            gap: .75rem;
            align-items: center;
        }

        .urg-search {
            position: relative;
        }

        .urg-search i {
            position: absolute;
            top: 50%;
            right: .9rem;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        .urg-search input {
            width: 100%;
            min-height: 44px;
            padding: .65rem 2.55rem .65rem 1rem;
            border: 1px solid #dbe3ea;
            border-radius: 999px;
            outline: 0;
            background: #fff;
            color: #111827;
            font-size: .9rem;
            font-weight: 700;
        }

        .urg-search input:focus {
            border-color: #fb923c;
            box-shadow: 0 0 0 .2rem rgba(249, 115, 22, .1);
        }

        .urg-filters {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .urg-filter,
        .urg-counter,
        .urg-section-items {
            min-height: 36px;
            padding: .4rem .75rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .78rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .urg-filter:hover,
        .urg-filter.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .urg-counter {
            background: #f8fafc;
        }

        .urg-sections {
            display: grid;
            gap: 1rem;
        }

        .urg-section-card {
            overflow: hidden;
        }

        .urg-section-head {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .urg-section-title {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            min-width: 0;
        }

        .urg-section-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .urg-section-icon.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .urg-section-icon.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .urg-section-icon.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .urg-section-icon.empty {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #94a3b8;
        }

        .urg-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .urg-section-head p {
            margin: .2rem 0 0;
            color: #64748b;
            font-size: .84rem;
            line-height: 1.6;
        }

        .urg-section-meta {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .urg-section-count {
            min-width: 56px;
            min-height: 38px;
            padding: .45rem .75rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 950;
        }

        .urg-section-count.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .urg-section-count.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .urg-section-count.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .urg-section-count.empty {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #94a3b8;
        }

        .urg-items {
            display: grid;
            gap: .65rem;
            padding: .9rem;
            background: #fbfdff;
        }

        .urg-item {
            padding: .9rem;
            border-radius: 18px;
            background: #fff;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .urg-item:hover {
            color: inherit;
            transform: translateY(-1px);
            border-color: #fed7aa;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .06);
        }

        .urg-item-main {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            min-width: 0;
        }

        .urg-item-status {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .urg-item-status.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .urg-item-status.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .urg-item-status.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .urg-item-status.empty {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #94a3b8;
        }

        .urg-item h6 {
            margin: 0;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .urg-item p {
            margin: .2rem 0 0;
            color: #64748b;
            font-size: .82rem;
            line-height: 1.7;
        }

        .urg-item-side {
            display: flex;
            align-items: center;
            gap: .55rem;
            flex-shrink: 0;
        }

        .urg-count {
            min-width: 54px;
            min-height: 36px;
            border-radius: 999px;
            padding: .4rem .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 950;
        }

        .urg-count.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .urg-count.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .urg-count.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .urg-count.empty {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #94a3b8;
        }

        .urg-chevron {
            color: #fb923c;
            font-size: .82rem;
        }

        .urg-empty,
        .urg-empty-filter {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .urg-empty-icon,
        .urg-empty-filter i,
        .urg-empty-inline i {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .urg-empty-filter i {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #94a3b8;
        }

        .urg-empty h5,
        .urg-empty-filter h5 {
            color: #111827;
            font-weight: 950;
            margin-bottom: .45rem;
        }

        .urg-empty p,
        .urg-empty-filter p {
            max-width: 560px;
            margin: 0 auto;
            color: #64748b;
            line-height: 1.8;
        }

        .urg-empty-inline {
            padding: 2rem 1rem;
            border-radius: 18px;
            border: 1px dashed #cbd5e1;
            background: #fff;
            text-align: center;
        }

        .urg-empty-inline i {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            font-size: 1.35rem;
        }

        .urg-empty-inline strong {
            display: block;
            color: #111827;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .urg-empty-inline span {
            color: #64748b;
            font-size: .84rem;
        }

        @media (max-width: 1199.98px) {
            .urg-hero {
                flex-direction: column;
                align-items: stretch;
            }

            .urg-priority-card {
                min-width: 0;
            }

            .urg-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .urg-toolbar {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .urg-hero,
            .urg-metric,
            .urg-toolbar,
            .urg-section-card,
            .urg-empty,
            .urg-empty-filter {
                border-radius: 18px;
            }

            .urg-hero,
            .urg-section-head {
                padding: 1rem;
            }

            .urg-hero-main,
            .urg-section-head,
            .urg-item {
                flex-direction: column;
                align-items: stretch;
            }

            .urg-hero-copy h3 {
                font-size: 1.2rem;
            }

            .urg-metrics {
                grid-template-columns: 1fr;
            }

            .urg-section-meta {
                justify-content: flex-start;
            }

            .urg-item-side {
                justify-content: space-between;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = {!! json_encode($jsLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

            const searchInput = document.getElementById('urgentSearchInput');
            const filterButtons = document.querySelectorAll('.urg-filter');
            const sections = Array.from(document.querySelectorAll('.urg-searchable-section'));
            const items = Array.from(document.querySelectorAll('.urg-searchable-item'));
            const emptyFilter = document.getElementById('urgentEmptyFilter');
            const visibleCounter = document.getElementById('urgentVisibleCounter');

            let activeFilter = 'all';

            function matchesTone(element) {
                if (activeFilter === 'all') {
                    return true;
                }

                if (activeFilter === 'active') {
                    return Number(element.dataset.count || element.dataset.sectionTotal || 0) > 0;
                }

                return element.dataset.tone === activeFilter;
            }

            function applyFilters() {
                const term = searchInput && searchInput.value ? searchInput.value.trim().toLowerCase() : '';
                let visibleItems = 0;
                let visibleSections = 0;

                sections.forEach(function (section) {
                    let sectionHasVisibleItems = false;
                    const sectionSearch = section.dataset.search || '';
                    const sectionTextMatches = sectionSearch.includes(term);

                    section.querySelectorAll('.urg-searchable-item').forEach(function (item) {
                        const itemSearch = item.dataset.search || '';
                        const textMatch = !term || itemSearch.includes(term) || sectionTextMatches;
                        const toneMatch = matchesTone(item);

                        const shouldShow = textMatch && toneMatch;

                        item.classList.toggle('d-none', !shouldShow);

                        if (shouldShow) {
                            visibleItems++;
                            sectionHasVisibleItems = true;
                        }
                    });

                    const sectionToneMatch = matchesTone(section);
                    const showSection = sectionHasVisibleItems || (term && sectionTextMatches && sectionToneMatch);

                    section.classList.toggle('d-none', !showSection);

                    if (showSection) {
                        visibleSections++;
                    }
                });

                if (visibleCounter) {
                    visibleCounter.textContent = `${labels.visible} ${visibleItems}`;
                }

                if (emptyFilter) {
                    emptyFilter.classList.toggle('d-none', visibleSections > 0);
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    filterButtons.forEach(function (btn) {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');
                    activeFilter = this.dataset.filter || 'all';

                    applyFilters();
                });
            });

            applyFilters();
        });
    </script>
@endsection