@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إدارة المخازن' : 'Warehouse Management')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة المخازن' : 'Warehouse Management')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $warehouses->count();
    $totalCount = method_exists($warehouses, 'total') ? $warehouses->total() : $warehouses->count();

    $metrics = [
        'total' => (int) ($warehouseMetrics['total'] ?? $totalCount),
        'main' => (int) ($warehouseMetrics['main'] ?? 0),
        'pending_profiles' => (int) ($warehouseMetrics['pending_profiles'] ?? 0),
        'approved_profiles' => (int) ($warehouseMetrics['approved_profiles'] ?? 0),
    ];

    $warehouseLocation = static function ($warehouse) use ($locale) {
        $parts = collect([
            optional($warehouse->governorate)->name ?? null,
            optional($warehouse->city)->{'name_' . $locale} ?? null,
            optional($warehouse->state)->{'name_' . $locale} ?? null,
            optional($warehouse->country)->{'name_' . $locale} ?? null,
        ])->filter()->values();

        return $parts->isNotEmpty() ? $parts->implode(', ') : '-';
    };

    $warehouseScore = static function ($warehouse) {
        $checks = [
            filled($warehouse->name),
            filled($warehouse->phone),
            filled($warehouse->latitude) && filled($warehouse->longitude),
            filled($warehouse->governorate_id ?? null) || (bool) $warehouse->governorate,
            filled($warehouse->city_id ?? null) || (bool) $warehouse->city,
            filled($warehouse->state_id ?? null) || (bool) $warehouse->state,
            filled($warehouse->country_id ?? null) || (bool) $warehouse->country,
            (bool) data_get($warehouse, 'businessProfile'),
        ];

        return (int) round((collect($checks)->filter()->count() / count($checks)) * 100);
    };

    $profileLabel = static function ($status) use ($text) {
        $status = is_string($status) ? strtolower($status) : '';

        return match ($status) {
            'approved' => $text('Approved', 'معتمد'),
            'pending' => $text('Pending', 'قيد المراجعة'),
            'rejected' => $text('Rejected', 'مرفوض'),
            default => $text('No Profile', 'لا يوجد ملف'),
        };
    };

    $profileTone = static function ($status) {
        $status = is_string($status) ? strtolower($status) : '';

        return match ($status) {
            'approved' => 'approved',
            'pending' => 'pending',
            'rejected' => 'rejected',
            default => 'empty',
        };
    };
@endphp

@section('page-actions')
    <a href="{{ route('admin.settings.warehouses.create') }}" class="btn whx-primary-action">
        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
        {{ $text('Add New Warehouse', 'إضافة مخزن جديد') }}
    </a>
@endsection

@section('content')
    <div class="whx-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="whx-hero">
            <div class="whx-hero-main">
                <div class="whx-hero-icon">
                    <i class="fas fa-warehouse"></i>
                </div>

                <div class="whx-hero-copy">
                    <span class="whx-kicker">{{ $text('Warehouse operations', 'عمليات المخازن') }}</span>
                    <h3>{{ $text('Warehouse Control Center', 'مركز التحكم في المخازن') }}</h3>
                    <p>
                        {{ $text(
                            'Manage locations, main warehouse assignment, profile status, and operational readiness from one clean control page.',
                            'أدر المواقع وتحديد المخزن الرئيسي وحالة الملفات وجاهزية التشغيل من صفحة واحدة واضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="whx-main-card">
                <span>{{ $text('Main warehouse', 'المخزن الرئيسي') }}</span>
                <strong>{{ $mainWarehouse?->name ?? $text('Not configured yet', 'لم يتم تحديده بعد') }}</strong>
                <small>
                    {{ $mainWarehouse?->full_address ?? $text('Create a warehouse and mark it as main to show it here.', 'أنشئ مخزنًا وحدده كمخزن رئيسي ليظهر هنا.') }}
                </small>
            </div>
        </section>

        <section class="whx-metrics">
            <div class="whx-metric">
                <i class="fas fa-warehouse"></i>
                <div>
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ number_format($metrics['total']) }}</strong>
                </div>
            </div>

            <div class="whx-metric">
                <i class="fas fa-star"></i>
                <div>
                    <span>{{ $text('Main', 'الرئيسي') }}</span>
                    <strong>{{ number_format($metrics['main']) }}</strong>
                </div>
            </div>

            <div class="whx-metric">
                <i class="fas fa-clock"></i>
                <div>
                    <span>{{ $text('Pending profiles', 'قيد المراجعة') }}</span>
                    <strong>{{ number_format($metrics['pending_profiles']) }}</strong>
                </div>
            </div>

            <div class="whx-metric">
                <i class="fas fa-clipboard-check"></i>
                <div>
                    <span>{{ $text('Approved profiles', 'ملفات معتمدة') }}</span>
                    <strong>{{ number_format($metrics['approved_profiles']) }}</strong>
                </div>
            </div>
        </section>

        <section class="whx-toolbar">
            <div class="whx-search">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="whxSearchInput"
                    placeholder="{{ $text('Search by name, phone, location, status...', 'ابحث بالاسم أو الهاتف أو الموقع أو الحالة...') }}"
                >
            </div>

            <div class="whx-filters">
                <button type="button" class="whx-filter active" data-filter="all">{{ $text('All', 'الكل') }}</button>
                <button type="button" class="whx-filter" data-filter="main">{{ $text('Main', 'رئيسي') }}</button>
                <button type="button" class="whx-filter" data-filter="regular">{{ $text('Regular', 'عادي') }}</button>
                <button type="button" class="whx-filter" data-filter="pending">{{ $text('Pending', 'قيد المراجعة') }}</button>
                <button type="button" class="whx-filter" data-filter="approved">{{ $text('Approved', 'معتمد') }}</button>
            </div>

            <div class="whx-view-toggle">
                <button type="button" class="whx-view-btn active" data-view="table">
                    <i class="fas fa-table"></i>
                </button>
                <button type="button" class="whx-view-btn" data-view="cards">
                    <i class="fas fa-grip"></i>
                </button>
            </div>
        </section>

        <section class="whx-list-card">
            <div class="whx-list-head">
                <div>
                    <h5>{{ $text('All Warehouses', 'كل المخازن') }}</h5>
                    <p>
                        {{ $text(
                            'Review contact, location, profile status, and readiness for every warehouse.',
                            'راجع بيانات التواصل والموقع وحالة الملف وجاهزية كل مخزن.'
                        ) }}
                    </p>
                </div>

                <div class="whx-list-actions">
                    <span id="whxVisibleCounter" class="whx-count-pill">
                        {{ $visibleCount }} / {{ $totalCount }}
                    </span>

                    <a href="{{ route('admin.settings.warehouses.create') }}" class="btn whx-primary-action">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Add Warehouse', 'إضافة مخزن') }}
                    </a>
                </div>
            </div>

            @if ($warehouses->count() > 0)
                <div class="whx-table-view" id="whxTableView">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 whx-table">
                            <thead>
                                <tr>
                                    <th>{{ $text('Warehouse', 'المخزن') }}</th>
                                    <th>{{ $text('Contact', 'التواصل') }}</th>
                                    <th>{{ $text('Location', 'الموقع') }}</th>
                                    <th>{{ $text('Status', 'الحالة') }}</th>
                                    <th>{{ $text('Profile', 'الملف') }}</th>
                                    <th>{{ $text('Readiness', 'الجاهزية') }}</th>
                                    <th class="text-end">{{ $text('Actions', 'الإجراءات') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($warehouses as $warehouse)
                                    @php
                                        $profileStatus = data_get($warehouse, 'businessProfile.status');
                                        $profileStatusKey = is_string($profileStatus) ? strtolower($profileStatus) : '';
                                        $profileToneValue = $profileTone($profileStatusKey);
                                        $score = $warehouseScore($warehouse);
                                        $location = $warehouseLocation($warehouse);
                                        $isMain = (bool) $warehouse->is_main;
                                        $mapUrl = $warehouse->latitude && $warehouse->longitude
                                            ? 'https://www.google.com/maps?q=' . $warehouse->latitude . ',' . $warehouse->longitude
                                            : null;
                                    @endphp

                                    <tr
                                        class="whx-row"
                                        data-search="{{ strtolower($warehouse->name . ' ' . $warehouse->phone . ' ' . $location . ' ' . $profileStatusKey . ' ' . ($isMain ? 'main' : 'regular')) }}"
                                        data-status="{{ $isMain ? 'main' : 'regular' }}"
                                        data-profile="{{ $profileToneValue }}"
                                    >
                                        <td>
                                            <div class="whx-name-cell">
                                                <span class="whx-avatar">
                                                    <i class="fas fa-warehouse"></i>
                                                </span>

                                                <div>
                                                    <strong>{{ $warehouse->name }}</strong>
                                                    <small>ID #{{ $warehouse->id }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="whx-contact">
                                                @if($warehouse->phone)
                                                    <a href="tel:{{ $warehouse->phone }}">
                                                        <i class="fas fa-phone"></i>
                                                        {{ $warehouse->phone }}
                                                    </a>
                                                @else
                                                    <span>-</span>
                                                @endif

                                                @if($warehouse->latitude && $warehouse->longitude)
                                                    <small>{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</small>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="whx-location">
                                                <span>{{ $location }}</span>

                                                @if($mapUrl)
                                                    <a href="{{ $mapUrl }}" target="_blank">
                                                        <i class="fas fa-map-marker-alt {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                                        {{ $text('Map', 'الخريطة') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <span class="whx-badge {{ $isMain ? 'main' : 'regular' }}">
                                                <i class="fas fa-{{ $isMain ? 'star' : 'circle' }}"></i>
                                                {{ $isMain ? $text('Main', 'رئيسي') : $text('Regular', 'عادي') }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="whx-badge profile-{{ $profileToneValue }}">
                                                <i class="fas fa-clipboard-check"></i>
                                                {{ $profileLabel($profileStatusKey) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="whx-score">
                                                <div>
                                                    <span>{{ $text('Ready', 'جاهز') }}</span>
                                                    <strong>{{ $score }}%</strong>
                                                </div>
                                                <em><b style="width: {{ $score }}%"></b></em>
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <div class="whx-actions">
                                                <form action="{{ route('admin.settings.warehouses.toggle-status', $warehouse->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="btn whx-icon-btn whx-star-btn"
                                                        title="{{ $isMain ? $text('Unset as main', 'إلغاء الرئيسي') : $text('Set as main', 'تحديد كرئيسي') }}"
                                                    >
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>

                                                <a
                                                    href="{{ route('admin.settings.warehouses.edit', $warehouse->id) }}"
                                                    class="btn whx-icon-btn whx-edit-btn"
                                                    title="{{ $text('Edit warehouse', 'تعديل المخزن') }}"
                                                >
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn whx-icon-btn whx-delete-btn js-warehouse-delete-btn"
                                                    data-action="{{ route('admin.settings.warehouses.destroy', $warehouse->id) }}"
                                                    data-title="{{ $warehouse->name }}"
                                                    data-message="{{ $text('Delete this warehouse?', 'هل تريد حذف هذا المخزن؟') }}"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="whx-card-view d-none" id="whxCardView">
                    @foreach ($warehouses as $warehouse)
                        @php
                            $profileStatus = data_get($warehouse, 'businessProfile.status');
                            $profileStatusKey = is_string($profileStatus) ? strtolower($profileStatus) : '';
                            $profileToneValue = $profileTone($profileStatusKey);
                            $score = $warehouseScore($warehouse);
                            $location = $warehouseLocation($warehouse);
                            $isMain = (bool) $warehouse->is_main;
                        @endphp

                        <article
                            class="whx-mobile-card whx-row"
                            data-search="{{ strtolower($warehouse->name . ' ' . $warehouse->phone . ' ' . $location . ' ' . $profileStatusKey . ' ' . ($isMain ? 'main' : 'regular')) }}"
                            data-status="{{ $isMain ? 'main' : 'regular' }}"
                            data-profile="{{ $profileToneValue }}"
                        >
                            <div class="whx-mobile-top">
                                <div class="whx-name-cell">
                                    <span class="whx-avatar">
                                        <i class="fas fa-warehouse"></i>
                                    </span>

                                    <div>
                                        <strong>{{ $warehouse->name }}</strong>
                                        <small>ID #{{ $warehouse->id }}</small>
                                    </div>
                                </div>

                                <span class="whx-badge {{ $isMain ? 'main' : 'regular' }}">
                                    <i class="fas fa-{{ $isMain ? 'star' : 'circle' }}"></i>
                                    {{ $isMain ? $text('Main', 'رئيسي') : $text('Regular', 'عادي') }}
                                </span>
                            </div>

                            <div class="whx-mobile-info">
                                <div>
                                    <span>{{ $text('Contact', 'التواصل') }}</span>
                                    <strong>{{ $warehouse->phone ?: '-' }}</strong>
                                </div>

                                <div>
                                    <span>{{ $text('Location', 'الموقع') }}</span>
                                    <strong>{{ $location }}</strong>
                                </div>

                                <div>
                                    <span>{{ $text('Profile', 'الملف') }}</span>
                                    <strong>{{ $profileLabel($profileStatusKey) }}</strong>
                                </div>

                                <div>
                                    <span>{{ $text('Readiness', 'الجاهزية') }}</span>
                                    <strong>{{ $score }}%</strong>
                                </div>
                            </div>

                            <div class="whx-mobile-actions">
                                <form action="{{ route('admin.settings.warehouses.toggle-status', $warehouse->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn whx-star-btn">
                                        <i class="fas fa-star {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                        {{ $text('Main', 'رئيسي') }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.settings.warehouses.edit', $warehouse->id) }}" class="btn whx-edit-btn">
                                    <i class="fas fa-pen {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                    {{ $text('Edit', 'تعديل') }}
                                </a>

                                <button
                                    type="button"
                                    class="btn whx-delete-btn js-warehouse-delete-btn"
                                    data-action="{{ route('admin.settings.warehouses.destroy', $warehouse->id) }}"
                                    data-title="{{ $warehouse->name }}"
                                    data-message="{{ $text('Delete this warehouse?', 'هل تريد حذف هذا المخزن؟') }}"
                                >
                                    <i class="fas fa-trash {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                    {{ $text('Delete', 'حذف') }}
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="whx-empty-filter d-none" id="whxEmptyFilter">
                    <i class="fas fa-search"></i>
                    <h5>{{ $text('No matching warehouses', 'لا توجد مخازن مطابقة') }}</h5>
                    <p>{{ $text('Try changing the search term or selected filter.', 'جرّب تغيير كلمة البحث أو الفلتر المحدد.') }}</p>
                </div>

                <div class="whx-pagination">
                    {{ $warehouses->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="whx-empty">
                    <div class="whx-empty-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>

                    <h5>{{ $text('No warehouses found', 'لا توجد مخازن') }}</h5>

                    <p>
                        {{ $text(
                            'Create the first warehouse to start controlling location, shipping, and the main warehouse flag.',
                            'أنشئ أول مخزن للتحكم في الموقع والشحن وتحديد المخزن الرئيسي.'
                        ) }}
                    </p>

                    <a href="{{ route('admin.settings.warehouses.create') }}" class="btn whx-primary-action">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Add Warehouse', 'إضافة مخزن') }}
                    </a>
                </div>
            @endif
        </section>
    </div>

    <div class="modal fade" id="warehouseDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content whx-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">{{ $text('Confirm delete', 'تأكيد الحذف') }}</h5>
                        <p class="whx-modal-subtitle mb-0" id="warehouseDeleteSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" id="warehouseDeleteForm">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <div class="whx-warning-box">
                            <div><i class="fas fa-warehouse"></i></div>
                            <p id="warehouseDeleteMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn whx-light-btn" data-bs-dismiss="modal">
                            {{ $text('Cancel', 'إلغاء') }}
                        </button>

                        <button type="submit" class="btn whx-danger-action">
                            {{ $text('Delete', 'حذف') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style data-page-style>
        .whx-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .whx-hero,
        .whx-metric,
        .whx-toolbar,
        .whx-list-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .whx-hero {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .whx-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .whx-hero-icon,
        .whx-avatar,
        .whx-empty-icon {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .whx-hero-icon {
            width: 62px;
            height: 62px;
            border-radius: 22px;
            font-size: 1.45rem;
        }

        .whx-kicker {
            display: inline-flex;
            padding: .28rem .75rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 900;
        }

        .whx-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .whx-hero-copy p {
            margin: .45rem 0 0;
            max-width: 780px;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .whx-main-card {
            min-width: 290px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .whx-main-card span,
        .whx-metric span,
        .whx-mobile-info span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .whx-main-card strong {
            display: block;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .whx-main-card small {
            display: block;
            margin-top: .2rem;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.6;
        }

        .whx-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .whx-metric {
            min-height: 96px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .whx-metric i {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whx-metric strong {
            display: block;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            line-height: 1;
        }

        .whx-toolbar {
            padding: .9rem;
            display: grid;
            grid-template-columns: minmax(220px, 1fr) auto auto;
            gap: .75rem;
            align-items: center;
        }

        .whx-search {
            position: relative;
        }

        .whx-search i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        [dir="ltr"] .whx-search i {
            left: .9rem;
        }

        [dir="rtl"] .whx-search i {
            right: .9rem;
        }

        .whx-search input {
            width: 100%;
            min-height: 44px;
            border: 1px solid #dbe3ea;
            border-radius: 999px;
            background: #fff;
            color: #111827;
            font-size: .9rem;
            font-weight: 700;
            outline: 0;
        }

        [dir="ltr"] .whx-search input {
            padding: .65rem 1rem .65rem 2.55rem;
        }

        [dir="rtl"] .whx-search input {
            padding: .65rem 2.55rem .65rem 1rem;
        }

        .whx-search input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .whx-filters,
        .whx-view-toggle,
        .whx-list-actions {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .whx-filter,
        .whx-view-btn,
        .whx-count-pill {
            min-height: 36px;
            padding: .4rem .75rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .78rem;
            font-weight: 950;
        }

        .whx-view-btn {
            width: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whx-filter:hover,
        .whx-filter.active,
        .whx-view-btn:hover,
        .whx-view-btn.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .whx-list-card {
            overflow: hidden;
        }

        .whx-list-head {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .whx-list-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .whx-list-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .whx-primary-action,
        .whx-light-btn,
        .whx-danger-action {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whx-primary-action {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .whx-primary-action:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .whx-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .whx-danger-action {
            background: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #fff !important;
        }

        .whx-table {
            min-width: 1080px;
            table-layout: fixed;
        }

        .whx-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 1px solid #e5e7eb !important;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .whx-table tbody td {
            padding: 1rem;
            border-color: #eef2f7 !important;
            vertical-align: middle;
        }

        .whx-table tbody tr:hover {
            background: #fbfdff;
        }

        .whx-name-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .whx-avatar {
            width: 44px;
            height: 44px;
            border-radius: 16px;
        }

        .whx-name-cell strong,
        .whx-mobile-info strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .whx-name-cell small {
            display: block;
            color: #64748b;
            font-size: .76rem;
        }

        .whx-contact a,
        .whx-contact span {
            display: block;
            color: #111827;
            font-size: .86rem;
            font-weight: 850;
            text-decoration: none;
            line-height: 1.5;
        }

        .whx-contact small,
        .whx-location span {
            display: block;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.6;
        }

        .whx-location a {
            display: inline-flex;
            align-items: center;
            margin-top: .35rem;
            color: #1d4ed8;
            font-size: .76rem;
            font-weight: 900;
            text-decoration: none;
        }

        .whx-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .whx-badge.main,
        .whx-badge.profile-approved {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .whx-badge.regular {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .whx-badge.profile-pending {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .whx-badge.profile-rejected {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .whx-badge.profile-empty {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        .whx-score {
            min-width: 96px;
        }

        .whx-score div {
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .35rem;
        }

        .whx-score span {
            color: #64748b;
            font-size: .72rem;
            font-weight: 950;
        }

        .whx-score strong {
            color: #111827;
            font-size: .78rem;
            font-weight: 950;
        }

        .whx-score em {
            display: block;
            height: 7px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .whx-score b {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #2563eb;
        }

        .whx-actions {
            display: flex;
            justify-content: flex-end;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .whx-icon-btn {
            width: 38px;
            height: 38px;
            padding: 0 !important;
            border-radius: 14px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whx-star-btn {
            background: #fffbeb !important;
            border: 1px solid #fde68a !important;
            color: #b45309 !important;
        }

        .whx-edit-btn {
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
        }

        .whx-delete-btn {
            background: #fef2f2 !important;
            border: 1px solid #fecaca !important;
            color: #b91c1c !important;
        }

        .whx-star-btn:hover,
        .whx-edit-btn:hover,
        .whx-delete-btn:hover {
            color: #fff !important;
        }

        .whx-star-btn:hover {
            background: #b45309 !important;
            border-color: #b45309 !important;
        }

        .whx-edit-btn:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
        }

        .whx-delete-btn:hover {
            background: #b91c1c !important;
            border-color: #b91c1c !important;
        }

        .whx-card-view {
            padding: 1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }

        .whx-mobile-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
        }

        .whx-mobile-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .whx-mobile-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .65rem;
        }

        .whx-mobile-info div {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .whx-mobile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: .9rem;
        }

        .whx-mobile-actions .btn {
            min-height: 38px;
            border-radius: 999px !important;
            font-weight: 900 !important;
            font-size: .78rem;
        }

        .whx-empty,
        .whx-empty-filter {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .whx-empty-icon,
        .whx-empty-filter i {
            width: 78px;
            height: 78px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            font-size: 1.8rem;
        }

        .whx-empty-filter i {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whx-empty h5,
        .whx-empty-filter h5 {
            color: #111827;
            font-weight: 950;
            margin-bottom: .45rem;
        }

        .whx-empty p,
        .whx-empty-filter p {
            max-width: 560px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .whx-pagination {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: center;
            border-top: 1px solid #e5e7eb;
        }

        .whx-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .whx-modal .modal-title {
            color: #111827;
            font-weight: 950;
        }

        .whx-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .whx-warning-box {
            display: flex;
            align-items: flex-start;
            gap: .85rem;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
        }

        .whx-warning-box div {
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

        .whx-warning-box p {
            margin: 0;
            color: #334155;
            line-height: 1.7;
            font-weight: 800;
        }

        @media (max-width: 1199.98px) {
            .whx-hero {
                flex-direction: column;
                align-items: stretch;
            }

            .whx-main-card {
                min-width: 0;
            }

            .whx-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .whx-toolbar {
                grid-template-columns: 1fr;
            }

            .whx-card-view {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .whx-hero,
            .whx-metric,
            .whx-toolbar,
            .whx-list-card {
                border-radius: 18px;
            }

            .whx-hero,
            .whx-list-head {
                padding: 1rem;
            }

            .whx-hero-main {
                flex-direction: column;
            }

            .whx-hero-copy h3 {
                font-size: 1.2rem;
            }

            .whx-metrics {
                grid-template-columns: 1fr;
            }

            .whx-list-head {
                flex-direction: column;
                align-items: stretch;
            }

            .whx-list-actions,
            .whx-list-actions .btn {
                width: 100%;
            }

            .whx-list-actions .btn {
                justify-content: center;
            }

            .whx-mobile-info {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('whxSearchInput');
            const filterButtons = document.querySelectorAll('.whx-filter');
            const viewButtons = document.querySelectorAll('.whx-view-btn');
            const tableView = document.getElementById('whxTableView');
            const cardView = document.getElementById('whxCardView');
            const emptyFilter = document.getElementById('whxEmptyFilter');
            const visibleCounter = document.getElementById('whxVisibleCounter');

            let activeFilter = 'all';

            function allRows() {
                return Array.from(document.querySelectorAll('.whx-row'));
            }

            function matchesFilter(row) {
                if (activeFilter === 'all') {
                    return true;
                }

                if (activeFilter === 'main' || activeFilter === 'regular') {
                    return row.dataset.status === activeFilter;
                }

                return row.dataset.profile === activeFilter;
            }

            function applyFilters() {
                const term = (searchInput?.value || '').trim().toLowerCase();
                let visible = 0;

                allRows().forEach(function (row) {
                    const search = row.dataset.search || '';
                    const show = search.includes(term) && matchesFilter(row);

                    row.classList.toggle('d-none', !show);

                    if (show) {
                        visible++;
                    }
                });

                if (visibleCounter) {
                    visibleCounter.textContent = String(visible);
                }

                if (emptyFilter) {
                    emptyFilter.classList.toggle('d-none', visible > 0);
                }
            }

            searchInput?.addEventListener('input', applyFilters);

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    activeFilter = this.dataset.filter || 'all';
                    applyFilters();
                });
            });

            viewButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    viewButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const view = this.dataset.view;

                    tableView?.classList.toggle('d-none', view !== 'table');
                    cardView?.classList.toggle('d-none', view !== 'cards');
                });
            });

            const modalEl = document.getElementById('warehouseDeleteModal');

            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                const form = document.getElementById('warehouseDeleteForm');
                const subtitle = document.getElementById('warehouseDeleteSubtitle');
                const message = document.getElementById('warehouseDeleteMessage');

                document.addEventListener('click', function (event) {
                    const button = event.target.closest('.js-warehouse-delete-btn');

                    if (!button) {
                        return;
                    }

                    event.preventDefault();

                    if (form) {
                        form.action = button.getAttribute('data-action') || '';
                    }

                    if (subtitle) {
                        subtitle.textContent = button.getAttribute('data-title') || '';
                    }

                    if (message) {
                        message.textContent = button.getAttribute('data-message') || '';
                    }

                    modal.show();
                });
            }

            applyFilters();
        });
    </script>
@endsection
