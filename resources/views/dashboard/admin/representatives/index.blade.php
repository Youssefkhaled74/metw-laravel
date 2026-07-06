@extends('layouts.admin')

@section('title', 'المناديب')
@section('page-title', 'إدارة المناديب')

@php
    $sortBy = $sortBy ?? request('sort_by', 'account_number');
    $sortDir = $sortDir ?? request('sort_dir', 'asc');

    $nextSortDir = fn (string $column) => $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';

    $sortUrl = fn (string $column) => route('admin.representatives.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), [
        'sort_by' => $column,
        'sort_dir' => $nextSortDir($column),
    ]));

    $accountTypeLabel = fn ($value) => match ($value) {
        'free' => 'مندوب حر',
        'warehouse' => 'مندوب مستودع',
        default => '--',
    };

    $statusLabel = fn ($value) => match ($value) {
        'incomplete' => 'غير مكتمل',
        'pending_review' => 'قيد المراجعة',
        'approved' => 'معتمد',
        'rejected' => 'مرفوض',
        'suspended' => 'موقوف',
        default => '--',
    };

    $statusTone = fn ($value) => match ($value) {
        'approved' => 'approved',
        'pending_review' => 'pending',
        'rejected' => 'rejected',
        'suspended' => 'suspended',
        'incomplete' => 'incomplete',
        default => 'empty',
    };

    $accountTone = fn ($value) => match ($value) {
        'free' => 'free',
        'warehouse' => 'warehouse',
        default => 'empty',
    };

    $representativeName = function ($representative) {
        return trim(collect([
            $representative->first_name,
            $representative->father_name,
            $representative->last_name,
        ])->filter()->implode(' ')) ?: '--';
    };

    $visibleCount = $representatives->count();
    $totalCount = method_exists($representatives, 'total') ? $representatives->total() : $representatives->count();

$allRepresentatives = $representatives instanceof \Illuminate\Pagination\AbstractPaginator
    ? collect($representatives->items())
    : collect($representatives);
    $metrics = [
        'total' => $totalCount,
        'approved' => $allRepresentatives->filter(function ($representative) {
            $status = $representative->status?->value ?? $representative->status;
            return $status === 'approved';
        })->count(),
        'pending' => $allRepresentatives->filter(function ($representative) {
            $status = $representative->status?->value ?? $representative->status;
            return $status === 'pending_review';
        })->count(),
        'warehouse' => $allRepresentatives->filter(function ($representative) {
            $type = $representative->account_type?->value ?? $representative->account_type;
            return $type === 'warehouse';
        })->count(),
    ];

    $hasFilters =
        request()->filled('account_number') ||
        request()->filled('name') ||
        request()->filled('mobile') ||
        (request('account_type') && request('account_type') !== 'all') ||
        (request('work_type') && request('work_type') !== 'all') ||
        (request('status') && request('status') !== 'all') ||
        request()->filled('governorate_id') ||
        request()->filled('city_id');

    $jsLabels = [
        'allCount' => 'كل النتائج',
        'visible' => 'ظاهر',
        'noMatches' => 'لا توجد نتائج مطابقة للبحث الحالي',
    ];
@endphp

@section('content')
    <div class="rep-page" dir="rtl">
        <section class="rep-hero">
            <div class="rep-hero-main">
                <div class="rep-hero-icon">
                    <i class="fas fa-user-tie"></i>
                </div>

                <div class="rep-hero-copy">
                    <span class="rep-chip">إدارة التشغيل</span>
                    <h3>مركز إدارة المناديب</h3>
                    <p>
                        عرض ومراجعة حسابات المناديب، نوع الحساب، حالة الاعتماد، نطاق التغطية، وبيانات العمل من شاشة واحدة منظمة.
                    </p>
                </div>
            </div>

            <div class="rep-hero-side">
                <span>إجمالي النتائج</span>
                <strong>{{ number_format($totalCount) }}</strong>
                <small>{{ $visibleCount }} ظاهر في الصفحة الحالية</small>
            </div>
        </section>

        <section class="rep-metrics">
            <div class="rep-metric">
                <div class="rep-metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <span>إجمالي المناديب</span>
                    <strong>{{ number_format($metrics['total']) }}</strong>
                    <small>كل الحسابات المطابقة للفلاتر.</small>
                </div>
            </div>

            <div class="rep-metric">
                <div class="rep-metric-icon success">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div>
                    <span>معتمدين</span>
                    <strong>{{ number_format($metrics['approved']) }}</strong>
                    <small>ظاهرين في الصفحة الحالية.</small>
                </div>
            </div>

            <div class="rep-metric">
                <div class="rep-metric-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <span>قيد المراجعة</span>
                    <strong>{{ number_format($metrics['pending']) }}</strong>
                    <small>يحتاجون متابعة.</small>
                </div>
            </div>

            <div class="rep-metric">
                <div class="rep-metric-icon info">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div>
                    <span>مناديب مستودع</span>
                    <strong>{{ number_format($metrics['warehouse']) }}</strong>
                    <small>مرتبطين بعمليات المستودعات.</small>
                </div>
            </div>
        </section>

        <section class="rep-filter-card">
            <div class="rep-section-head">
                <div>
                    <h5>البحث والتصفية</h5>
                    <p>استخدم الفلاتر للوصول السريع إلى حسابات المناديب حسب الحالة أو الموقع أو نوع الحساب.</p>
                </div>

                @if($hasFilters)
                    <a href="{{ route('admin.representatives.index') }}" class="btn rep-light-btn">
                        <i class="fas fa-rotate-right ms-1"></i>
                        إعادة ضبط
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('admin.representatives.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="rep-label">رقم الحساب</label>
                        <div class="rep-input-icon">
                            <i class="fas fa-hashtag"></i>
                            <input
                                type="text"
                                name="account_number"
                                value="{{ request('account_number') }}"
                                class="form-control rep-control"
                                placeholder="بحث برقم الحساب"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">الاسم</label>
                        <div class="rep-input-icon">
                            <i class="fas fa-user"></i>
                            <input
                                type="text"
                                name="name"
                                value="{{ request('name') }}"
                                class="form-control rep-control"
                                placeholder="بحث بالاسم"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">الموبايل</label>
                        <div class="rep-input-icon">
                            <i class="fas fa-phone"></i>
                            <input
                                type="text"
                                name="mobile"
                                value="{{ request('mobile') }}"
                                class="form-control rep-control"
                                placeholder="بحث بالموبايل"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">نوع الحساب</label>
                        <select name="account_type" class="form-select rep-control">
                            <option value="all">الكل</option>
                            <option value="free" @selected(request('account_type') === 'free')>مندوب حر</option>
                            <option value="warehouse" @selected(request('account_type') === 'warehouse')>مندوب مستودع</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">نوع العمل</label>
                        <select name="work_type" class="form-select rep-control">
                            <option value="all">الكل</option>
                            @foreach($workTypes as $workType)
                                <option value="{{ $workType->code }}" @selected(request('work_type') === $workType->code)>
                                    {{ $workType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">الحالة</label>
                        <select name="status" class="form-select rep-control">
                            <option value="all">الكل</option>
                            <option value="incomplete" @selected(request('status') === 'incomplete')>غير مكتمل</option>
                            <option value="pending_review" @selected(request('status') === 'pending_review')>قيد المراجعة</option>
                            <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                            <option value="suspended" @selected(request('status') === 'suspended')>موقوف</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">المحافظة</label>
                        <select name="governorate_id" class="form-select rep-control">
                            <option value="">الكل</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" @selected(request('governorate_id') == $governorate->id)>
                                    {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="rep-label">المدينة</label>
                        <select name="city_id" class="form-select rep-control">
                            <option value="">الكل</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="rep-filter-actions">
                            <button type="submit" class="btn rep-primary-btn">
                                <i class="fas fa-search ms-1"></i>
                                بحث
                            </button>

                            <a href="{{ route('admin.representatives.index') }}" class="btn rep-light-btn">
                                إلغاء الفلاتر
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="rep-toolbar">
            <div class="rep-live-search">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="repSearchInput"
                    placeholder="بحث سريع داخل النتائج الحالية..."
                >
            </div>

            <div class="rep-quick-filters">
                <button type="button" class="rep-filter active" data-filter="all">الكل</button>
                <button type="button" class="rep-filter" data-filter="approved">معتمد</button>
                <button type="button" class="rep-filter" data-filter="pending_review">قيد المراجعة</button>
                <button type="button" class="rep-filter" data-filter="rejected">مرفوض</button>
                <button type="button" class="rep-filter" data-filter="suspended">موقوف</button>
                <button type="button" class="rep-filter" data-filter="warehouse">مندوب مستودع</button>
            </div>

            <div class="rep-view-toggle">
                <button type="button" class="rep-view-btn active" data-view="table" title="Table">
                    <i class="fas fa-table"></i>
                </button>

                <button type="button" class="rep-view-btn" data-view="cards" title="Cards">
                    <i class="fas fa-grip"></i>
                </button>
            </div>
        </section>

        <section class="rep-list-card">
            <div class="rep-list-head">
                <div>
                    <h5>قائمة المناديب</h5>
                    <p>اضغط على رقم الحساب أو زر العرض لمراجعة بيانات المندوب كاملة.</p>
                </div>

                <div class="rep-list-actions">
                    <span class="rep-count-pill" id="repVisibleCounter">
                        {{ $visibleCount }} / {{ $totalCount }}
                    </span>
                </div>
            </div>

            @if($representatives->count() > 0)
                <div class="rep-table-view" id="repTableView">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 rep-table">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ $sortUrl('account_number') }}" class="rep-sort-link">
                                            رقم الحساب
                                            @if($sortBy === 'account_number')
                                                <i class="fas fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>الاسم</th>
                                    <th>الموبايل</th>
                                    <th>نوع الحساب</th>
                                    <th>نوع العمل</th>
                                    <th>الحالة</th>
                                    <th>المحافظات</th>
                                    <th>المدن</th>
                                    <th class="text-end">إجراءات</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($representatives as $representative)
                                    @php
                                        $accountTypeValue = $representative->account_type?->value ?? $representative->account_type;
                                        $statusValue = $representative->status?->value ?? $representative->status;
                                        $name = $representativeName($representative);
                                        $workTypesText = $representative->workTypes->pluck('option.name')->filter()->implode('، ') ?: '--';
                                        $governoratesText = $representative->governorates->pluck('name')->filter()->implode('، ') ?: '--';
                                        $citiesText = $representative->cities->pluck('name')->filter()->implode('، ') ?: '--';
                                        $searchText = strtolower($representative->account_number . ' ' . $name . ' ' . $representative->phone . ' ' . $accountTypeLabel($accountTypeValue) . ' ' . $workTypesText . ' ' . $statusLabel($statusValue) . ' ' . $governoratesText . ' ' . $citiesText);
                                    @endphp

                                    <tr
                                        class="rep-row"
                                        data-search="{{ $searchText }}"
                                        data-status="{{ $statusValue }}"
                                        data-account-type="{{ $accountTypeValue }}"
                                    >
                                        <td>
                                            <a href="{{ route('admin.representatives.show', $representative) }}" class="rep-account-link">
                                                {{ $representative->account_number }}
                                            </a>
                                        </td>

                                        <td>
                                            <div class="rep-name-cell">
                                                <span class="rep-avatar">
                                                    {{ \Illuminate\Support\Str::substr($name !== '--' ? $name : 'M', 0, 1) }}
                                                </span>

                                                <div>
                                                    <strong>{{ $name }}</strong>
                                                    <small>{{ $representative->account_number }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($representative->phone)
                                                <a href="tel:{{ $representative->phone }}" class="rep-phone">
                                                    <i class="fas fa-phone ms-1"></i>
                                                    {{ $representative->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="rep-badge account-{{ $accountTone($accountTypeValue) }}">
                                                {{ $accountTypeLabel($accountTypeValue) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="rep-muted-text">
                                                {{ $workTypesText }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="rep-badge status-{{ $statusTone($statusValue) }}">
                                                {{ $statusLabel($statusValue) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="rep-muted-text">
                                                {{ $governoratesText }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="rep-muted-text">
                                                {{ $citiesText }}
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <a href="{{ route('admin.representatives.show', $representative) }}" class="btn rep-icon-btn" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rep-card-view d-none" id="repCardView">
                    @foreach($representatives as $representative)
                        @php
                            $accountTypeValue = $representative->account_type?->value ?? $representative->account_type;
                            $statusValue = $representative->status?->value ?? $representative->status;
                            $name = $representativeName($representative);
                            $workTypesText = $representative->workTypes->pluck('option.name')->filter()->implode('، ') ?: '--';
                            $governoratesText = $representative->governorates->pluck('name')->filter()->implode('، ') ?: '--';
                            $citiesText = $representative->cities->pluck('name')->filter()->implode('، ') ?: '--';
                            $searchText = strtolower($representative->account_number . ' ' . $name . ' ' . $representative->phone . ' ' . $accountTypeLabel($accountTypeValue) . ' ' . $workTypesText . ' ' . $statusLabel($statusValue) . ' ' . $governoratesText . ' ' . $citiesText);
                        @endphp

                        <article
                            class="rep-mobile-card rep-row"
                            data-search="{{ $searchText }}"
                            data-status="{{ $statusValue }}"
                            data-account-type="{{ $accountTypeValue }}"
                        >
                            <div class="rep-mobile-top">
                                <div class="rep-name-cell">
                                    <span class="rep-avatar">
                                        {{ \Illuminate\Support\Str::substr($name !== '--' ? $name : 'M', 0, 1) }}
                                    </span>

                                    <div>
                                        <strong>{{ $name }}</strong>
                                        <small>{{ $representative->account_number }}</small>
                                    </div>
                                </div>

                                <span class="rep-badge status-{{ $statusTone($statusValue) }}">
                                    {{ $statusLabel($statusValue) }}
                                </span>
                            </div>

                            <div class="rep-mobile-grid">
                                <div>
                                    <span>الموبايل</span>
                                    <strong>{{ $representative->phone ?: '--' }}</strong>
                                </div>

                                <div>
                                    <span>نوع الحساب</span>
                                    <strong>{{ $accountTypeLabel($accountTypeValue) }}</strong>
                                </div>

                                <div>
                                    <span>نوع العمل</span>
                                    <strong>{{ $workTypesText }}</strong>
                                </div>

                                <div>
                                    <span>المحافظات</span>
                                    <strong>{{ $governoratesText }}</strong>
                                </div>

                                <div>
                                    <span>المدن</span>
                                    <strong>{{ $citiesText }}</strong>
                                </div>
                            </div>

                            <div class="rep-mobile-actions">
                                <a href="{{ route('admin.representatives.show', $representative) }}" class="btn rep-primary-btn w-100">
                                    <i class="fas fa-eye ms-1"></i>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="rep-empty-filter d-none" id="repEmptyFilter">
                    <i class="fas fa-search"></i>
                    <h5>لا توجد نتائج مطابقة</h5>
                    <p>جرّب تغيير البحث السريع أو الفلتر المحدد.</p>
                </div>

                <div class="rep-pagination">
                    {{ $representatives->links() }}
                </div>
            @else
                <div class="rep-empty">
                    <div class="rep-empty-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>

                    <h5>لا توجد نتائج مطابقة</h5>
                    <p>لا يوجد مناديب مطابقين للفلاتر الحالية. جرّب إعادة ضبط البحث.</p>

                    <a href="{{ route('admin.representatives.index') }}" class="btn rep-primary-btn">
                        إعادة ضبط
                    </a>
                </div>
            @endif
        </section>
    </div>

    <style data-page-style>
        .rep-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .rep-hero,
        .rep-metric,
        .rep-filter-card,
        .rep-toolbar,
        .rep-list-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .rep-hero {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .rep-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .rep-hero-icon {
            width: 62px;
            height: 62px;
            border-radius: 22px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            flex-shrink: 0;
        }

        .rep-chip {
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

        .rep-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .rep-hero-copy p {
            max-width: 820px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .rep-hero-side {
            min-width: 230px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .rep-hero-side span,
        .rep-metric span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .rep-hero-side strong {
            display: block;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            line-height: 1;
        }

        .rep-hero-side small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            margin-top: .5rem;
        }

        .rep-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .rep-metric {
            min-height: 106px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: .8rem;
        }

        .rep-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rep-metric-icon.success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .rep-metric-icon.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .rep-metric-icon.info {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .rep-metric strong {
            display: block;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 950;
            line-height: 1;
        }

        .rep-metric small {
            display: block;
            color: #64748b;
            font-size: .76rem;
            margin-top: .45rem;
            line-height: 1.5;
        }

        .rep-filter-card {
            padding: 1.25rem;
        }

        .rep-section-head,
        .rep-list-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .rep-section-head {
            margin-bottom: 1rem;
        }

        .rep-section-head h5,
        .rep-list-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .rep-section-head p,
        .rep-list-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.7;
        }

        .rep-label {
            display: block;
            margin-bottom: .42rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 950;
        }

        .rep-control {
            min-height: 44px;
            border-radius: 14px !important;
            border-color: #dbe3ea !important;
            color: #111827 !important;
            background: #fff !important;
            font-size: .88rem;
            font-weight: 700;
            box-shadow: none !important;
        }

        .rep-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1) !important;
        }

        .rep-input-icon {
            position: relative;
        }

        .rep-input-icon i {
            position: absolute;
            top: 50%;
            right: .85rem;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        .rep-input-icon .rep-control {
            padding-right: 2.45rem;
        }

        .rep-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
            padding-top: .25rem;
        }

        .rep-primary-btn,
        .rep-light-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rep-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .rep-primary-btn:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .rep-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .rep-light-btn:hover {
            background: #f8fafc !important;
            color: #111827 !important;
        }

        .rep-toolbar {
            padding: .9rem;
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto auto;
            gap: .75rem;
            align-items: center;
        }

        .rep-live-search {
            position: relative;
        }

        .rep-live-search i {
            position: absolute;
            top: 50%;
            right: .9rem;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .rep-live-search input {
            width: 100%;
            min-height: 44px;
            padding: .65rem 2.55rem .65rem 1rem;
            border: 1px solid #dbe3ea;
            border-radius: 999px;
            outline: 0;
            color: #111827;
            font-size: .9rem;
            font-weight: 700;
        }

        .rep-live-search input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .rep-quick-filters,
        .rep-view-toggle,
        .rep-list-actions {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .rep-filter,
        .rep-view-btn,
        .rep-count-pill {
            min-height: 36px;
            padding: .4rem .75rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .78rem;
            font-weight: 950;
        }

        .rep-view-btn {
            width: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rep-filter:hover,
        .rep-filter.active,
        .rep-view-btn:hover,
        .rep-view-btn.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .rep-list-card {
            overflow: hidden;
        }

        .rep-list-head {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .rep-table {
            min-width: 1160px;
            table-layout: fixed;
        }

        .rep-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 1px solid #e5e7eb !important;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .rep-table tbody td {
            padding: 1rem;
            border-color: #eef2f7 !important;
            vertical-align: middle;
            font-size: .86rem;
        }

        .rep-table tbody tr:hover {
            background: #fbfdff;
        }

        .rep-sort-link,
        .rep-account-link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 950;
        }

        .rep-sort-link {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            color: #334155;
        }

        .rep-name-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .rep-avatar {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            font-weight: 950;
            flex-shrink: 0;
        }

        .rep-name-cell strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .rep-name-cell small {
            display: block;
            color: #64748b;
            font-size: .75rem;
        }

        .rep-phone {
            display: inline-flex;
            align-items: center;
            color: #111827;
            font-weight: 850;
            text-decoration: none;
        }

        .rep-phone:hover {
            color: #1d4ed8;
        }

        .rep-muted-text {
            color: #475569;
            font-size: .82rem;
            line-height: 1.7;
            max-width: 160px;
        }

        .rep-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .rep-badge.account-free {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .rep-badge.account-warehouse {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
        }

        .rep-badge.account-empty,
        .rep-badge.status-empty {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        .rep-badge.status-approved {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .rep-badge.status-pending {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .rep-badge.status-rejected,
        .rep-badge.status-suspended {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .rep-badge.status-incomplete {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #64748b;
        }

        .rep-icon-btn {
            width: 38px;
            height: 38px;
            padding: 0 !important;
            border-radius: 14px !important;
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rep-icon-btn:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }

        .rep-card-view {
            padding: 1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }

        .rep-mobile-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
        }

        .rep-mobile-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .rep-mobile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .65rem;
        }

        .rep-mobile-grid div {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .rep-mobile-grid span {
            display: block;
            color: #64748b;
            font-size: .73rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .rep-mobile-grid strong {
            display: block;
            color: #111827;
            font-size: .82rem;
            font-weight: 850;
            line-height: 1.6;
        }

        .rep-mobile-actions {
            margin-top: .9rem;
        }

        .rep-empty,
        .rep-empty-filter {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .rep-empty-icon,
        .rep-empty-filter i {
            width: 78px;
            height: 78px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            font-size: 1.8rem;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rep-empty h5,
        .rep-empty-filter h5 {
            color: #111827;
            font-weight: 950;
            margin-bottom: .45rem;
        }

        .rep-empty p,
        .rep-empty-filter p {
            max-width: 560px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .rep-pagination {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: center;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 1199.98px) {
            .rep-hero {
                flex-direction: column;
                align-items: stretch;
            }

            .rep-hero-side {
                min-width: 0;
            }

            .rep-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .rep-toolbar {
                grid-template-columns: 1fr;
            }

            .rep-card-view {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .rep-hero,
            .rep-metric,
            .rep-filter-card,
            .rep-toolbar,
            .rep-list-card {
                border-radius: 18px;
            }

            .rep-hero,
            .rep-filter-card,
            .rep-list-head {
                padding: 1rem;
            }

            .rep-hero-main {
                flex-direction: column;
            }

            .rep-hero-copy h3 {
                font-size: 1.2rem;
            }

            .rep-metrics {
                grid-template-columns: 1fr;
            }

            .rep-section-head,
            .rep-list-head {
                flex-direction: column;
                align-items: stretch;
            }

            .rep-filter-actions {
                justify-content: stretch;
            }

            .rep-filter-actions .btn,
            .rep-list-actions,
            .rep-list-actions .btn {
                width: 100%;
            }

            .rep-mobile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = {!! json_encode($jsLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

            const searchInput = document.getElementById('repSearchInput');
            const filterButtons = document.querySelectorAll('.rep-filter');
            const viewButtons = document.querySelectorAll('.rep-view-btn');
            const tableView = document.getElementById('repTableView');
            const cardView = document.getElementById('repCardView');
            const emptyFilter = document.getElementById('repEmptyFilter');
            const visibleCounter = document.getElementById('repVisibleCounter');

            let activeFilter = 'all';

            function rows() {
                return Array.from(document.querySelectorAll('.rep-row'));
            }

            function matchesFilter(row) {
                if (activeFilter === 'all') {
                    return true;
                }

                if (activeFilter === 'warehouse') {
                    return row.dataset.accountType === 'warehouse';
                }

                return row.dataset.status === activeFilter;
            }

            function applyFilters() {
                const term = (searchInput && searchInput.value ? searchInput.value : '').trim().toLowerCase();
                let visible = 0;

                rows().forEach(function (row) {
                    const searchText = row.dataset.search || '';
                    const shouldShow = searchText.includes(term) && matchesFilter(row);

                    row.classList.toggle('d-none', !shouldShow);

                    if (shouldShow) {
                        visible++;
                    }
                });

                if (visibleCounter) {
                    visibleCounter.textContent = labels.visible + ' ' + visible;
                }

                if (emptyFilter) {
                    emptyFilter.classList.toggle('d-none', visible > 0);
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

            viewButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    viewButtons.forEach(function (btn) {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');

                    const view = this.dataset.view || 'table';

                    if (tableView) {
                        tableView.classList.toggle('d-none', view !== 'table');
                    }

                    if (cardView) {
                        cardView.classList.toggle('d-none', view !== 'cards');
                    }
                });
            });

            applyFilters();
        });
    </script>
@endsection