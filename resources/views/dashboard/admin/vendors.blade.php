@extends('layouts.admin')

@section('title', __('admin-dashboard.vendors_management'))
@section('page-title', __('admin-dashboard.vendors_management'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $vendors->count();
    $totalCount = method_exists($vendors, 'total') ? $vendors->total() : $vendors->count();

    $activeVisibleCount = $vendors->filter(fn ($vendor) => (bool) $vendor->is_active)->count();
    $inactiveVisibleCount = $vendors->filter(fn ($vendor) => ! (bool) $vendor->is_active)->count();

    $sortBy = request('sort_by', 'created_at');
    $sortDir = request('sort_dir', 'desc');
    $statusFilter = request('status', 'all');

    $sortUrl = static function (string $column) use ($sortBy, $sortDir) {
        $nextDir = $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';

        return route('admin.vendors', array_merge(
            request()->except('page'),
            [
                'sort_by' => $column,
                'sort_dir' => $nextDir,
            ]
        ));
    };

    $sortIcon = static function (string $column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) {
            return 'fa-sort';
        }

        return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };

    $healthScore = static function ($vendor) {
        $checks = [
            filled($vendor->name),
            filled($vendor->email),
            filled($vendor->phone),
            filled($vendor->logo),
            (int) ($vendor->products_count ?? 0) > 0,
            (int) ($vendor->ecommerce_order_items_count ?? 0) > 0,
            (bool) $vendor->is_active,
        ];

        return (int) round((collect($checks)->filter()->count() / count($checks)) * 100);
    };

    $baseVendorsUrl = route('admin.vendors');

    $statusChips = [
        [
            'label' => $text('All', 'الكل'),
            'params' => ['status' => 'all'],
            'active' => $statusFilter === 'all',
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
    ];

    $sortChips = [
        [
            'label' => $text('Newest', 'الأحدث'),
            'params' => ['sort_by' => 'created_at', 'sort_dir' => 'desc'],
            'active' => $sortBy === 'created_at' && $sortDir === 'desc',
        ],
        [
            'label' => $text('Most products', 'الأكثر منتجات'),
            'params' => ['sort_by' => 'products', 'sort_dir' => 'desc'],
            'active' => $sortBy === 'products',
        ],
        [
            'label' => $text('Most orders', 'الأكثر طلبات'),
            'params' => ['sort_by' => 'orders', 'sort_dir' => 'desc'],
            'active' => $sortBy === 'orders',
        ],
    ];
@endphp

@section('page-actions')
    <a href="{{ route('admin.vendors.create') }}" class="btn vnd-add-btn">
        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
        {{ __('admin-dashboard.add_new_vendor') }}
    </a>
@endsection

@section('content')
    <div class="vnd-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="vnd-hero-card">
            <div class="vnd-hero-main">
                <div class="vnd-hero-icon">
                    <i class="fas fa-store"></i>
                </div>

                <div class="vnd-hero-text">
                    <span class="vnd-chip">
                        {{ $text('Vendors management', 'إدارة البائعين') }}
                    </span>

                    <h4>{{ __('admin-dashboard.vendors_management') }}</h4>

                    <p>
                        {{ $text(
                            'Search, review vendor activity, monitor status, and manage vendors from one clean screen.',
                            'ابحث وراجع نشاط البائعين وتابع حالتهم وأدرهم من شاشة واحدة واضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="vnd-metrics">
                <div class="vnd-metric-item">
                    <span>{{ $text('Visible', 'المعروض') }}</span>
                    <strong>{{ $visibleCount }}</strong>
                </div>

                <div class="vnd-metric-item">
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ $totalCount }}</strong>
                </div>

                <div class="vnd-metric-item">
                    <span>{{ __('admin-dashboard.active') }}</span>
                    <strong>{{ $activeVisibleCount }}</strong>
                </div>

                <div class="vnd-metric-item">
                    <span>{{ __('admin-dashboard.inactive') }}</span>
                    <strong>{{ $inactiveVisibleCount }}</strong>
                </div>
            </div>
        </section>

        <section class="vnd-filter-card">
            <div class="vnd-section-head">
                <div>
                    <h5>{{ $text('Search and filters', 'البحث والفلاتر') }}</h5>
                    <p>{{ $text('Use quick chips or detailed filters to find vendors faster.', 'استخدم الاختيارات السريعة أو الفلاتر للوصول للبائعين بسرعة.') }}</p>
                </div>

                @if(request('search') || request('status', 'all') !== 'all' || request('sort_by') || request('sort_dir'))
                    <a href="{{ $baseVendorsUrl }}" class="btn btn-sm vnd-clear-btn">
                        <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Reset', 'إعادة ضبط') }}
                    </a>
                @endif
            </div>

            <div class="vnd-chip-groups">
                <div class="vnd-quick-filters">
                    @foreach ($statusChips as $chip)
                        @php
                            $chipUrl = route('admin.vendors', array_merge(
                                request()->except(['page', 'status']),
                                $chip['params']
                            ));
                        @endphp

                        <a href="{{ $chipUrl }}" class="vnd-quick-chip {{ $chip['active'] ? 'active' : '' }}">
                            {{ $chip['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="vnd-quick-filters">
                    @foreach ($sortChips as $chip)
                        @php
                            $chipUrl = route('admin.vendors', array_merge(
                                request()->except(['page', 'sort_by', 'sort_dir']),
                                $chip['params']
                            ));
                        @endphp

                        <a href="{{ $chipUrl }}" class="vnd-quick-chip {{ $chip['active'] ? 'active' : '' }}">
                            {{ $chip['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <form method="GET" action="{{ route('admin.vendors') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-xl-5 col-lg-6">
                        <label class="vnd-label" for="vendorsSearch">
                            {{ $text('Search vendors', 'بحث البائعين') }}
                        </label>

                        <div class="vnd-input-icon">
                            <i class="fas fa-search"></i>

                            <input
                                id="vendorsSearch"
                                type="text"
                                name="search"
                                class="form-control vnd-control"
                                placeholder="{{ $text('Search by name, email, phone, or address...', 'ابحث بالاسم أو الإيميل أو الهاتف أو العنوان...') }}"
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-xl-3 col-lg-6">
                        <label class="vnd-label" for="statusFilter">
                            {{ __('admin-dashboard.vendor_status') }}
                        </label>

                        <select id="statusFilter" name="status" class="form-select vnd-control">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>
                                {{ $text('All statuses', 'كل الحالات') }}
                            </option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                {{ __('admin-dashboard.active') }}
                            </option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                {{ __('admin-dashboard.inactive') }}
                            </option>
                        </select>
                    </div>

                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                    <div class="col-12 col-xl-4">
                        <div class="vnd-filter-actions">
                            <button type="submit" class="btn btn-primary vnd-submit-btn">
                                <i class="fas fa-filter {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Apply', 'تطبيق') }}
                            </button>

                            <a href="{{ route('admin.vendors') }}" class="btn vnd-reset-btn">
                                <i class="fas fa-undo {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Reset', 'إعادة ضبط') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="vnd-table-card">
            <div class="vnd-section-head vnd-table-head">
                <div>
                    <h5>{{ __('admin-dashboard.all_vendors') }}</h5>
                    <p>
                        {{ $text(
                            'Important vendor information is grouped to keep the table easy to scan.',
                            'تم تجميع أهم بيانات البائع لتسهيل قراءة الجدول بسرعة.'
                        ) }}
                    </p>
                </div>

                <span class="vnd-page-count">
                    <i class="fas fa-store {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $visibleCount }} / {{ $totalCount }}
                </span>
            </div>

            @if ($vendors->count() > 0)
                <div class="table-responsive vnd-table-wrap">
                    <table class="table align-middle mb-0 vnd-table">
                        <thead>
                            <tr>
                                <th class="vnd-col-vendor">
                                    <a class="vnd-sort-link" href="{{ $sortUrl('id') }}">
                                        <span>{{ __('admin-dashboard.vendor_name') }}</span>
                                        <i class="fas {{ $sortIcon('id') }}"></i>
                                    </a>
                                </th>

                                <th class="vnd-col-contact">
                                    {{ $text('Contact', 'التواصل') }}
                                </th>

                                <th class="vnd-col-status">
                                    {{ __('admin-dashboard.vendor_status') }}
                                </th>

                                <th class="vnd-col-activity">
                                    {{ $text('Activity', 'النشاط') }}
                                </th>

                                <th class="vnd-col-health">
                                    {{ $text('Health', 'الجاهزية') }}
                                </th>

                                <th class="vnd-col-date">
                                    <a class="vnd-sort-link" href="{{ $sortUrl('created_at') }}">
                                        <span>{{ __('admin-dashboard.vendor_created_at') }}</span>
                                        <i class="fas {{ $sortIcon('created_at') }}"></i>
                                    </a>
                                </th>

                                <th class="vnd-col-actions text-end">
                                    {{ __('admin-dashboard.vendor_actions') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($vendors as $vendor)
                                @php
                                    $displayName = $vendor->name ?: $text('Vendor', 'بائع');
                                    $initial = mb_substr($displayName, 0, 1);
                                    $score = $healthScore($vendor);
                                @endphp

                                <tr>
                                    <td>
                                        <div class="vnd-vendor-cell">
                                            @if ($vendor->logo)
                                                <img src="{{ asset($vendor->logo) }}" alt="{{ $displayName }}" class="vnd-logo">
                                            @else
                                                <span class="vnd-avatar">{{ $initial }}</span>
                                            @endif

                                            <div class="vnd-vendor-info">
                                                <div class="vnd-vendor-name">{{ $displayName }}</div>

                                                <div class="vnd-vendor-meta">
                                                    <span class="vnd-vendor-number">
                                                        {{ $vendor->vendor_number ?: '#' . $vendor->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vnd-contact-stack">
                                            <div class="vnd-contact-line">
                                                <i class="far fa-envelope"></i>

                                                @if($vendor->email)
                                                    <a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>
                                                @else
                                                    <span>{{ $text('Not provided', 'غير متوفر') }}</span>
                                                @endif
                                            </div>

                                            <div class="vnd-contact-line">
                                                <i class="fas fa-phone"></i>

                                                @if($vendor->phone)
                                                    <a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>
                                                @else
                                                    <span>{{ $text('Not provided', 'غير متوفر') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="vnd-status vnd-status-{{ $vendor->is_active ? 'active' : 'inactive' }}">
                                            <i></i>
                                            {{ $vendor->is_active ? __('admin-dashboard.vendor_active') : __('admin-dashboard.vendor_inactive') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="vnd-activity-pills">
                                            <span class="vnd-count-pill products">
                                                <i class="fas fa-box"></i>
                                                {{ $vendor->products_count }}
                                            </span>

                                            <span class="vnd-count-pill orders">
                                                <i class="fas fa-shopping-cart"></i>
                                                {{ $vendor->ecommerce_order_items_count }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vnd-score">
                                            <div class="vnd-score-top">
                                                <span>{{ $text('Ready', 'جاهز') }}</span>
                                                <strong>{{ $score }}%</strong>
                                            </div>

                                            <div class="vnd-score-bar">
                                                <span style="width: {{ $score }}%"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vnd-date">
                                            @include('admin.partials.date', ['date' => $vendor->created_at])
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="vnd-actions-group">
                                            <a
                                                href="{{ route('admin.vendors.show', $vendor->id) }}"
                                                class="btn btn-sm vnd-view-btn"
                                                title="{{ __('admin-dashboard.vendor_view') }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                            >
                                                <i class="fas fa-eye"></i>
                                                <span>{{ $text('View', 'عرض') }}</span>
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm {{ $vendor->is_active ? 'vnd-pause-btn' : 'vnd-play-btn' }} js-vendor-status-btn"
                                                title="{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                data-action="{{ route('admin.vendors.toggle-status', $vendor->id) }}"
                                                data-vendor-name="{{ $displayName }}"
                                                data-message="{{ $vendor->is_active ? __('admin-dashboard.vendor_confirm_deactivate') : __('admin-dashboard.vendor_confirm_activate') }}"
                                                data-confirm-label="{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}"
                                                data-mode="{{ $vendor->is_active ? 'deactivate' : 'activate' }}"
                                            >
                                                <i class="fas fa-{{ $vendor->is_active ? 'pause' : 'play' }}"></i>
                                                <span>
                                                    {{ $vendor->is_active ? $text('Pause', 'إيقاف') : $text('Activate', 'تفعيل') }}
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="vnd-pagination">
                    {{ $vendors->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="vnd-empty">
                    <div class="vnd-empty-icon">
                        <i class="fas fa-store"></i>
                    </div>

                    <h5>{{ __('admin-dashboard.no_vendors_found') }}</h5>
                    <p>{{ __('admin-dashboard.no_vendors_message') }}</p>

                    <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary vnd-submit-btn">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.add_first_vendor') }}
                    </a>
                </div>
            @endif
        </section>
    </div>

    <div class="modal fade" id="vendorStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content vnd-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="vendorStatusModalTitle">
                            {{ $text('Confirm status change', 'تأكيد تغيير الحالة') }}
                        </h5>
                        <p class="vnd-modal-subtitle mb-0" id="vendorStatusModalSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" id="vendorStatusForm">
                    @csrf
                    @method('PATCH')

                    <div class="modal-body">
                        <div class="vnd-modal-warning">
                            <div>
                                <i class="fas fa-store"></i>
                            </div>

                            <p id="vendorStatusModalMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn vnd-reset-btn" data-bs-dismiss="modal">
                            {{ $text('Cancel', 'إلغاء') }}
                        </button>

                        <button type="submit" class="btn vnd-modal-confirm" id="vendorStatusConfirmBtn">
                            {{ $text('Confirm', 'تأكيد') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .vnd-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .vnd-add-btn {
            min-height: 44px;
            border-radius: 14px;
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .14);
        }

        .vnd-add-btn:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
        }

        .vnd-hero-card,
        .vnd-filter-card,
        .vnd-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .vnd-hero-card,
        .vnd-filter-card {
            padding: 1.25rem;
        }

        .vnd-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .vnd-hero-icon {
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

        .vnd-chip {
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

        .vnd-hero-text h4 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .vnd-hero-text p {
            margin: .45rem 0 0;
            max-width: 820px;
            color: #64748b;
            font-size: .95rem;
            line-height: 1.8;
        }

        .vnd-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1.15rem;
        }

        .vnd-metric-item {
            min-height: 74px;
            padding: .85rem 1rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .vnd-metric-item span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            margin-bottom: .35rem;
        }

        .vnd-metric-item strong {
            color: #111827;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
        }

        .vnd-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .vnd-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .vnd-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .88rem;
            line-height: 1.6;
        }

        .vnd-clear-btn,
        .vnd-reset-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-weight: 800;
        }

        .vnd-clear-btn:hover,
        .vnd-reset-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .vnd-chip-groups {
            display: grid;
            gap: .6rem;
            margin-bottom: 1rem;
        }

        .vnd-quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .vnd-quick-chip {
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

        .vnd-quick-chip:hover,
        .vnd-quick-chip.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .vnd-label {
            display: block;
            margin-bottom: .4rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
        }

        .vnd-control {
            min-height: 44px;
            border-radius: 13px;
            border-color: #dbe3ea;
            color: #111827;
            font-size: .9rem;
            box-shadow: none;
        }

        .vnd-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .vnd-input-icon {
            position: relative;
        }

        .vnd-input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        [dir="ltr"] .vnd-input-icon i {
            left: .85rem;
        }

        [dir="rtl"] .vnd-input-icon i {
            right: .85rem;
        }

        [dir="ltr"] .vnd-input-icon .vnd-control {
            padding-left: 2.5rem;
        }

        [dir="rtl"] .vnd-input-icon .vnd-control {
            padding-right: 2.5rem;
        }

        .vnd-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .vnd-submit-btn {
            min-height: 44px;
            border-radius: 13px;
            font-weight: 800;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .12);
        }

        .vnd-table-card {
            overflow: hidden;
        }

        .vnd-table-head {
            padding: 1.25rem 1.25rem 0;
        }

        .vnd-page-count {
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

        .vnd-table-wrap {
            border-top: 1px solid #e5e7eb;
        }

        .vnd-table {
            min-width: 1020px;
            table-layout: fixed;
        }

        .vnd-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .vnd-table tbody td {
            padding: 1rem;
            border-color: #eef2f7;
            vertical-align: middle;
        }

        .vnd-table tbody tr:hover {
            background: #fbfdff;
        }

        .vnd-col-vendor {
            width: 24%;
        }

        .vnd-col-contact {
            width: 24%;
        }

        .vnd-col-status {
            width: 12%;
        }

        .vnd-col-activity {
            width: 14%;
        }

        .vnd-col-health {
            width: 11%;
        }

        .vnd-col-date {
            width: 8%;
        }

        .vnd-col-actions {
            width: 7%;
        }

        .vnd-sort-link {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }

        .vnd-sort-link:hover {
            color: #1d4ed8;
        }

        .vnd-vendor-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .vnd-logo,
        .vnd-avatar {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            flex-shrink: 0;
        }

        .vnd-logo {
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .vnd-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 900;
            text-transform: uppercase;
        }

        .vnd-vendor-info {
            min-width: 0;
        }

        .vnd-vendor-name {
            color: #111827;
            font-size: .95rem;
            font-weight: 900;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vnd-vendor-meta {
            color: #64748b;
            font-size: .78rem;
            line-height: 1.5;
        }

        .vnd-vendor-number {
            color: #1d4ed8;
            font-weight: 900;
        }

        .vnd-contact-stack {
            display: grid;
            gap: .45rem;
        }

        .vnd-contact-line {
            display: flex;
            align-items: center;
            gap: .45rem;
            color: #64748b;
            font-size: .82rem;
            min-width: 0;
        }

        .vnd-contact-line i {
            color: #94a3b8;
            width: 16px;
            flex-shrink: 0;
        }

        .vnd-contact-line a {
            color: #334155;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .vnd-contact-line a:hover {
            color: #1d4ed8;
        }

        .vnd-status {
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
        }

        .vnd-status i {
            width: .45rem;
            height: .45rem;
            border-radius: 999px;
            background: currentColor;
        }

        .vnd-status-active {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .vnd-status-inactive {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .vnd-activity-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .vnd-count-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .vnd-count-pill.products {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .vnd-count-pill.orders {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .vnd-score {
            min-width: 90px;
        }

        .vnd-score-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .35rem;
        }

        .vnd-score-top span {
            color: #64748b;
            font-size: .72rem;
            font-weight: 900;
        }

        .vnd-score-top strong {
            color: #111827;
            font-size: .78rem;
            font-weight: 900;
        }

        .vnd-score-bar {
            height: 7px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .vnd-score-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #2563eb;
        }

        .vnd-date {
            color: #475569;
            font-size: .8rem;
            font-weight: 700;
        }

        .vnd-actions-group {
            display: flex;
            justify-content: flex-end;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .vnd-view-btn,
        .vnd-pause-btn,
        .vnd-play-btn {
            border-radius: 999px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            min-height: 34px;
            padding-inline: .8rem;
        }

        .vnd-view-btn {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .vnd-view-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .vnd-pause-btn {
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #b45309;
        }

        .vnd-pause-btn:hover {
            background: #b45309;
            border-color: #b45309;
            color: #fff;
        }

        .vnd-play-btn {
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            color: #047857;
        }

        .vnd-play-btn:hover {
            background: #047857;
            border-color: #047857;
            color: #fff;
        }

        .vnd-pagination {
            padding: 1rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            justify-content: center;
        }

        .vnd-empty {
            padding: 4rem 1.5rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .vnd-empty-icon {
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

        .vnd-empty h5 {
            color: #111827;
            font-weight: 900;
            margin-bottom: .45rem;
        }

        .vnd-empty p {
            max-width: 520px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .vnd-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .vnd-modal .modal-header,
        .vnd-modal .modal-footer {
            border-color: #e5e7eb;
        }

        .vnd-modal .modal-title {
            color: #111827;
            font-weight: 900;
        }

        .vnd-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .vnd-modal-warning {
            display: flex;
            align-items: flex-start;
            gap: .85rem;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
        }

        .vnd-modal-warning div {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .vnd-modal-warning p {
            margin: 0;
            color: #334155;
            line-height: 1.7;
            font-weight: 700;
        }

        .vnd-modal-confirm {
            border-radius: 999px;
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            font-weight: 900;
        }

        .vnd-modal-confirm.is-danger {
            background: #b45309;
            border-color: #b45309;
        }

        .vnd-modal-confirm.is-success {
            background: #047857;
            border-color: #047857;
        }

        @media (max-width: 1199.98px) {
            .vnd-table {
                min-width: 980px;
            }
        }

        @media (max-width: 767.98px) {
            .vnd-hero-card,
            .vnd-filter-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .vnd-hero-main {
                flex-direction: column;
            }

            .vnd-hero-text h4 {
                font-size: 1.2rem;
            }

            .vnd-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .vnd-section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .vnd-filter-actions {
                justify-content: flex-start;
            }

            .vnd-table-head {
                padding: 1rem 1rem 0;
            }

            .vnd-table {
                min-width: 920px;
            }

            .vnd-actions-group {
                justify-content: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    placement: 'top',
                    fallbackPlacements: []
                });
            });

            const modalEl = document.getElementById('vendorStatusModal');

            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('vendorStatusForm');
            const subtitle = document.getElementById('vendorStatusModalSubtitle');
            const message = document.getElementById('vendorStatusModalMessage');
            const confirmBtn = document.getElementById('vendorStatusConfirmBtn');

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-vendor-status-btn');

                if (!button) {
                    return;
                }

                event.preventDefault();

                form.action = button.getAttribute('data-action');
                subtitle.textContent = button.getAttribute('data-vendor-name') || '';
                message.textContent = button.getAttribute('data-message') || '';
                confirmBtn.textContent = button.getAttribute('data-confirm-label') || '{{ $text('Confirm', 'تأكيد') }}';

                confirmBtn.classList.remove('is-danger', 'is-success');

                if (button.getAttribute('data-mode') === 'deactivate') {
                    confirmBtn.classList.add('is-danger');
                } else {
                    confirmBtn.classList.add('is-success');
                }

                modal.show();
            });
        });
    </script>
@endsection