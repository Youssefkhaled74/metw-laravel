@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'أنواع النقل' : 'Transport Types')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة أنواع النقل' : 'Transport Types Management')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $sortBy = $sortBy ?? request('sort_by', 'created_at');
    $sortDir = $sortDir ?? request('sort_dir', 'desc');
    $statusFilter = request('status', 'all');

    $baseUrl = route('admin.settings.transport-types.index');

    $nextSortDir = function ($column) use ($sortBy, $sortDir) {
        return $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';
    };

    $sortIcon = function ($column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) {
            return 'fa-sort';
        }

        return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };

    $sortUrl = function (string $column) use ($nextSortDir) {
        return route('admin.settings.transport-types.index', array_merge(
            request()->except(['page', 'sort_by', 'sort_dir']),
            [
                'sort_by' => $column,
                'sort_dir' => $nextSortDir($column),
            ]
        ));
    };

    $capacityLabel = function ($transportType) use ($text) {
        if (data_get($transportType->metadata, 'unlimited_capacity')) {
            return $text('Unlimited capacity', 'سعة غير محدودة');
        }

        return trim(
            ($transportType->max_weight !== null ? $transportType->max_weight . ' kg' : '--')
            . ' / '
            . ($transportType->max_volume !== null ? $transportType->max_volume . ' m³' : '--')
        );
    };

    $capacityScore = static function ($transportType) {
        $checks = [
            filled($transportType->code),
            filled($transportType->name_en),
            filled($transportType->name_ar),
            filled($transportType->description),
            data_get($transportType->metadata, 'unlimited_capacity') || $transportType->max_weight !== null,
            data_get($transportType->metadata, 'unlimited_capacity') || $transportType->max_volume !== null,
            (bool) $transportType->is_active,
        ];

        return (int) round((collect($checks)->filter()->count() / count($checks)) * 100);
    };

    $visibleCount = $transportTypes->count();
    $totalCount = method_exists($transportTypes, 'total') ? $transportTypes->total() : $transportTypes->count();

    $activeVisibleCount = $transportTypes->filter(fn ($type) => (bool) $type->is_active)->count();
    $inactiveVisibleCount = $transportTypes->filter(fn ($type) => ! (bool) $type->is_active)->count();
    $unlimitedVisibleCount = $transportTypes->filter(fn ($type) => (bool) data_get($type->metadata, 'unlimited_capacity'))->count();

    $quickFilters = [
        [
            'label' => $text('All', 'الكل'),
            'params' => ['status' => 'all'],
            'active' => $statusFilter === 'all' && ! request('capacity'),
        ],
        [
            'label' => __('admin-dashboard.active'),
            'params' => ['status' => 'active'],
            'active' => $statusFilter === 'active',
        ],
        [
            'label' => __('admin-dashboard.inactive'),
            'params' => ['status' => 'inactive'],
            'active' => $statusFilter === 'inactive',
        ],
        [
            'label' => $text('Unlimited', 'غير محدود'),
            'params' => ['capacity' => 'unlimited'],
            'active' => request('capacity') === 'unlimited',
        ],
    ];
@endphp

@section('page-actions')
    <a href="{{ route('admin.settings.transport-types.create') }}" class="btn tti-add-btn">
        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
        {{ $text('Add Transport Type', 'إضافة نوع نقل') }}
    </a>
@endsection

@section('content')
    <div class="tti-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="tti-hero-card">
            <div class="tti-hero-main">
                <div class="tti-hero-icon">
                    <i class="fas fa-truck-fast"></i>
                </div>

                <div class="tti-hero-text">
                    <span class="tti-chip">
                        {{ $text('Transport settings', 'إعدادات النقل') }}
                    </span>

                    <h4>{{ $text('Transport Types Management', 'إدارة أنواع النقل') }}</h4>

                    <p>
                        {{ $text(
                            'Manage transport options, capacity limits, and availability from one clear screen.',
                            'أدر أنواع النقل وحدود السعة وحالة التفعيل من شاشة واحدة واضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="tti-metrics">
                <div class="tti-metric-item">
                    <span>{{ $text('Visible', 'المعروض') }}</span>
                    <strong>{{ $visibleCount }}</strong>
                </div>

                <div class="tti-metric-item">
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ $totalCount }}</strong>
                </div>

                <div class="tti-metric-item">
                    <span>{{ __('admin-dashboard.active') }}</span>
                    <strong>{{ $activeVisibleCount }}</strong>
                </div>

                <div class="tti-metric-item">
                    <span>{{ $text('Unlimited', 'غير محدود') }}</span>
                    <strong>{{ $unlimitedVisibleCount }}</strong>
                </div>
            </div>
        </section>

        <section class="tti-filter-card">
            <div class="tti-section-head">
                <div>
                    <h5>{{ $text('Search and filters', 'البحث والفلاتر') }}</h5>
                    <p>{{ $text('Search by code, name, or description. Use quick chips for faster filtering.', 'ابحث بالكود أو الاسم أو الوصف واستخدم الفلاتر السريعة.') }}</p>
                </div>

                @if(request('search') || request('status', 'all') !== 'all' || request('capacity') || request('sort_by') || request('sort_dir'))
                    <a href="{{ $baseUrl }}" class="btn btn-sm tti-clear-btn">
                        <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Reset', 'إعادة ضبط') }}
                    </a>
                @endif
            </div>

            <div class="tti-quick-filters">
                @foreach($quickFilters as $filter)
                    @php
                        $url = route('admin.settings.transport-types.index', array_merge(
                            request()->except(['page', 'status', 'capacity']),
                            $filter['params']
                        ));
                    @endphp

                    <a href="{{ $url }}" class="tti-quick-chip {{ $filter['active'] ? 'active' : '' }}">
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ $baseUrl }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-5">
                        <label class="tti-label">{{ $text('Search transport types', 'بحث أنواع النقل') }}</label>

                        <div class="tti-input-icon">
                            <i class="fas fa-search"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control tti-control"
                                placeholder="{{ $text('Search by code, name, or description...', 'ابحث بالكود أو الاسم أو الوصف...') }}"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="tti-label">{{ __('admin-dashboard.status') }}</label>

                        <select name="status" class="form-select tti-control">
                            <option value="all" @selected(request('status', 'all') === 'all')>
                                {{ $text('All statuses', 'كل الحالات') }}
                            </option>
                            <option value="active" @selected(request('status') === 'active')>
                                {{ __('admin-dashboard.active') }}
                            </option>
                            <option value="inactive" @selected(request('status') === 'inactive')>
                                {{ __('admin-dashboard.inactive') }}
                            </option>
                        </select>
                    </div>

                    @if(request('capacity'))
                        <input type="hidden" name="capacity" value="{{ request('capacity') }}">
                    @endif

                    <input type="hidden" name="sort_by" value="{{ request('sort_by', $sortBy) }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', $sortDir) }}">

                    <div class="col-12 col-lg-4">
                        <div class="tti-filter-actions">
                            <button type="submit" class="btn btn-primary tti-submit-btn">
                                <i class="fas fa-filter {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Filter', 'تصفية') }}
                            </button>

                            <a href="{{ $baseUrl }}" class="btn tti-reset-btn">
                                <i class="fas fa-undo {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Reset', 'إعادة ضبط') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="tti-table-card">
            <div class="tti-section-head tti-table-head">
                <div>
                    <h5>{{ $text('All transport types', 'جميع أنواع النقل') }}</h5>
                    <p>{{ $text('Important transport information is grouped to keep the table easy to scan.', 'تم تجميع أهم بيانات نوع النقل لتسهيل قراءة الجدول.') }}</p>
                </div>

                <span class="tti-page-count">
                    <i class="fas fa-truck-fast {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $visibleCount }} / {{ $totalCount }}
                </span>
            </div>

            @if ($transportTypes->count())
                <div class="table-responsive tti-table-wrap">
                    <table class="table align-middle mb-0 tti-table">
                        <thead>
                            <tr>
                                <th class="tti-col-name">
                                    <a class="tti-sort-link" href="{{ $sortUrl('code') }}">
                                        <span>{{ $text('Transport type', 'نوع النقل') }}</span>
                                        <i class="fas {{ $sortIcon('code') }}"></i>
                                    </a>
                                </th>

                                <th class="tti-col-desc">{{ $text('Description', 'الوصف') }}</th>
                                <th class="tti-col-capacity">{{ $text('Capacity', 'السعة') }}</th>
                                <th class="tti-col-health">{{ $text('Health', 'الجاهزية') }}</th>
                                <th class="tti-col-status">{{ __('admin-dashboard.status') }}</th>

                                <th class="tti-col-date">
                                    <a class="tti-sort-link" href="{{ $sortUrl('created_at') }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ $sortIcon('created_at') }}"></i>
                                    </a>
                                </th>

                                <th class="tti-col-actions text-end">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transportTypes as $transportType)
                                @php
                                    $score = $capacityScore($transportType);
                                    $isUnlimited = (bool) data_get($transportType->metadata, 'unlimited_capacity');
                                @endphp

                                <tr>
                                    <td>
                                        <div class="tti-type-cell">
                                            <span class="tti-type-icon">
                                                <i class="fas fa-truck-fast"></i>
                                            </span>

                                            <div class="tti-type-info">
                                                <div class="tti-type-name">
                                                    {{ $transportType->name_en }}
                                                </div>

                                                <div class="tti-type-meta">
                                                    <span>{{ $transportType->code }}</span>
                                                    @if ($transportType->name_ar)
                                                        <span class="tti-dot">•</span>
                                                        <span>{{ $transportType->name_ar }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="tti-description">
                                            {{ $transportType->description ? \Illuminate\Support\Str::limit($transportType->description, 90) : '--' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="tti-capacity-pill {{ $isUnlimited ? 'unlimited' : '' }}">
                                            <i class="fas {{ $isUnlimited ? 'fa-infinity' : 'fa-weight-hanging' }}"></i>
                                            {{ $capacityLabel($transportType) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="tti-score">
                                            <div class="tti-score-top">
                                                <span>{{ $text('Ready', 'جاهز') }}</span>
                                                <strong>{{ $score }}%</strong>
                                            </div>

                                            <div class="tti-score-bar">
                                                <span style="width: {{ $score }}%"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <form action="{{ route('admin.settings.transport-types.toggle-status', $transportType) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="tti-status tti-status-{{ $transportType->is_active ? 'active' : 'inactive' }}">
                                                <i></i>
                                                {{ $transportType->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="tti-date">
                                            @include('admin.partials.date', ['date' => $transportType->created_at])
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="tti-actions-group">
                                            <a
                                                href="{{ route('admin.settings.transport-types.edit', $transportType) }}"
                                                class="btn btn-sm tti-edit-btn"
                                                title="{{ $text('Edit', 'تعديل') }}"
                                            >
                                                <i class="fas fa-edit"></i>
                                                <span>{{ $text('Edit', 'تعديل') }}</span>
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm tti-delete-btn js-transport-delete-btn"
                                                data-action="{{ route('admin.settings.transport-types.destroy', $transportType) }}"
                                                data-title="{{ $transportType->name_en }}"
                                                data-message="{{ $text('Delete this transport type?', 'هل تريد حذف نوع النقل؟') }}"
                                                data-confirm-label="{{ $text('Delete', 'حذف') }}"
                                            >
                                                <i class="fas fa-trash"></i>
                                                <span>{{ $text('Delete', 'حذف') }}</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tti-pagination">
                    {{ $transportTypes->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="tti-empty">
                    <div class="tti-empty-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>

                    <h5>{{ $text('No transport types found yet', 'لا توجد أنواع نقل بعد') }}</h5>

                    <p>
                        {{ request('search') || request('status', 'all') !== 'all'
                            ? $text('No transport type matches the current filters.', 'لا يوجد نوع نقل مطابق للفلاتر الحالية.')
                            : $text('Start by adding your first transport type.', 'ابدأ بإضافة أول نوع نقل.')
                        }}
                    </p>

                    <a href="{{ route('admin.settings.transport-types.create') }}" class="btn btn-primary tti-submit-btn">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Add Transport Type', 'إضافة نوع نقل') }}
                    </a>
                </div>
            @endif
        </section>
    </div>

    <div class="modal fade" id="transportDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tti-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            {{ $text('Confirm delete', 'تأكيد الحذف') }}
                        </h5>

                        <p class="tti-modal-subtitle mb-0" id="transportDeleteModalSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" id="transportDeleteForm">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <div class="tti-modal-warning">
                            <div>
                                <i class="fas fa-truck-fast"></i>
                            </div>

                            <p id="transportDeleteModalMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn tti-reset-btn" data-bs-dismiss="modal">
                            {{ $text('Cancel', 'إلغاء') }}
                        </button>

                        <button type="submit" class="btn tti-modal-confirm" id="transportDeleteConfirmBtn">
                            {{ $text('Delete', 'حذف') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .tti-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .tti-add-btn {
            min-height: 44px;
            border-radius: 14px;
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .14);
        }

        .tti-add-btn:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }

        .tti-hero-card,
        .tti-filter-card,
        .tti-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .tti-hero-card,
        .tti-filter-card {
            padding: 1.25rem;
        }

        .tti-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .tti-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .tti-chip {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .28rem .7rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 800;
        }

        .tti-hero-text h4 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .tti-hero-text p {
            margin: .45rem 0 0;
            max-width: 820px;
            color: #64748b;
            font-size: .95rem;
            line-height: 1.8;
        }

        .tti-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1.15rem;
        }

        .tti-metric-item {
            min-height: 74px;
            padding: .85rem 1rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .tti-metric-item span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            margin-bottom: .35rem;
        }

        .tti-metric-item strong {
            color: #111827;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
        }

        .tti-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .tti-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .tti-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .88rem;
            line-height: 1.6;
        }

        .tti-clear-btn,
        .tti-reset-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-weight: 800;
        }

        .tti-clear-btn:hover,
        .tti-reset-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .tti-quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .tti-quick-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: .35rem .8rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .82rem;
            font-weight: 800;
            text-decoration: none;
        }

        .tti-quick-chip:hover,
        .tti-quick-chip.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .tti-label {
            display: block;
            margin-bottom: .4rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
        }

        .tti-control {
            min-height: 44px;
            border-radius: 13px;
            border-color: #dbe3ea;
            color: #111827;
            font-size: .9rem;
            box-shadow: none;
        }

        .tti-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .tti-input-icon {
            position: relative;
        }

        .tti-input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        [dir="ltr"] .tti-input-icon i {
            left: .85rem;
        }

        [dir="rtl"] .tti-input-icon i {
            right: .85rem;
        }

        [dir="ltr"] .tti-input-icon .tti-control {
            padding-left: 2.5rem;
        }

        [dir="rtl"] .tti-input-icon .tti-control {
            padding-right: 2.5rem;
        }

        .tti-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .tti-submit-btn {
            min-height: 44px;
            border-radius: 13px;
            font-weight: 800;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .12);
        }

        .tti-table-card {
            overflow: hidden;
        }

        .tti-table-head {
            padding: 1.25rem 1.25rem 0;
        }

        .tti-page-count {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .42rem .8rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .tti-table-wrap {
            border-top: 1px solid #e5e7eb;
        }

        .tti-table {
            min-width: 980px;
            table-layout: fixed;
        }

        .tti-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .tti-table tbody td {
            padding: 1rem;
            border-color: #eef2f7;
            vertical-align: middle;
        }

        .tti-table tbody tr:hover {
            background: #fbfdff;
        }

        .tti-col-name {
            width: 24%;
        }

        .tti-col-desc {
            width: 23%;
        }

        .tti-col-capacity {
            width: 16%;
        }

        .tti-col-health {
            width: 12%;
        }

        .tti-col-status {
            width: 10%;
        }

        .tti-col-date {
            width: 8%;
        }

        .tti-col-actions {
            width: 7%;
        }

        .tti-sort-link {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }

        .tti-sort-link:hover {
            color: #1d4ed8;
        }

        .tti-type-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .tti-type-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tti-type-info {
            min-width: 0;
        }

        .tti-type-name {
            color: #111827;
            font-size: .95rem;
            font-weight: 900;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tti-type-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .25rem;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.5;
        }

        .tti-type-meta span:first-child {
            color: #1d4ed8;
            font-weight: 900;
        }

        .tti-dot {
            color: #cbd5e1 !important;
            padding-inline: .15rem;
        }

        .tti-description {
            color: #64748b;
            font-size: .84rem;
            line-height: 1.7;
        }

        .tti-capacity-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .7rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .tti-capacity-pill.unlimited {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .tti-score {
            min-width: 90px;
        }

        .tti-score-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .35rem;
        }

        .tti-score-top span {
            color: #64748b;
            font-size: .72rem;
            font-weight: 900;
        }

        .tti-score-top strong {
            color: #111827;
            font-size: .78rem;
            font-weight: 900;
        }

        .tti-score-bar {
            height: 7px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .tti-score-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #2563eb;
        }

        .tti-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .42rem .75rem;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: .78rem;
            font-weight: 900;
            white-space: nowrap;
            cursor: pointer;
        }

        .tti-status i {
            width: .45rem;
            height: .45rem;
            border-radius: 999px;
            background: currentColor;
        }

        .tti-status-active {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .tti-status-inactive {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }

        .tti-date {
            color: #475569;
            font-size: .8rem;
            font-weight: 700;
        }

        .tti-actions-group {
            display: flex;
            justify-content: flex-end;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .tti-edit-btn,
        .tti-delete-btn {
            border-radius: 999px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            min-height: 34px;
            padding-inline: .8rem;
        }

        .tti-edit-btn {
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #b45309;
        }

        .tti-edit-btn:hover {
            background: #b45309;
            border-color: #b45309;
            color: #fff;
        }

        .tti-delete-btn {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .tti-delete-btn:hover {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
        }

        .tti-pagination {
            padding: 1rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            justify-content: center;
        }

        .tti-empty {
            padding: 4rem 1.5rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .tti-empty-icon {
            width: 78px;
            height: 78px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
        }

        .tti-empty h5 {
            color: #111827;
            font-weight: 900;
            margin-bottom: .45rem;
        }

        .tti-empty p {
            max-width: 520px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .tti-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .tti-modal .modal-header,
        .tti-modal .modal-footer {
            border-color: #e5e7eb;
        }

        .tti-modal .modal-title {
            color: #111827;
            font-weight: 900;
        }

        .tti-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .tti-modal-warning {
            display: flex;
            align-items: flex-start;
            gap: .85rem;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
        }

        .tti-modal-warning div {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #fef2f2;
            color: #b91c1c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tti-modal-warning p {
            margin: 0;
            color: #334155;
            line-height: 1.7;
            font-weight: 700;
        }

        .tti-modal-confirm {
            border-radius: 999px;
            background: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
            font-weight: 900;
        }

        .tti-modal-confirm:hover {
            background: #991b1b;
            border-color: #991b1b;
            color: #fff;
        }

        @media (max-width: 1199.98px) {
            .tti-table {
                min-width: 940px;
            }
        }

        @media (max-width: 767.98px) {
            .tti-hero-card,
            .tti-filter-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .tti-hero-main {
                flex-direction: column;
            }

            .tti-hero-text h4 {
                font-size: 1.2rem;
            }

            .tti-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tti-section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .tti-filter-actions {
                justify-content: flex-start;
            }

            .tti-table-head {
                padding: 1rem 1rem 0;
            }

            .tti-table {
                min-width: 900px;
            }

            .tti-actions-group {
                justify-content: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('transportDeleteModal');

            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('transportDeleteForm');
            const subtitle = document.getElementById('transportDeleteModalSubtitle');
            const message = document.getElementById('transportDeleteModalMessage');
            const confirmBtn = document.getElementById('transportDeleteConfirmBtn');

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-transport-delete-btn');

                if (!button) {
                    return;
                }

                event.preventDefault();

                form.action = button.getAttribute('data-action');
                subtitle.textContent = button.getAttribute('data-title') || '';
                message.textContent = button.getAttribute('data-message') || '';
                confirmBtn.textContent = button.getAttribute('data-confirm-label') || '{{ $text('Delete', 'حذف') }}';

                modal.show();
            });
        });
    </script>
@endsection