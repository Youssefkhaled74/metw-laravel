@extends('layouts.admin')

@section('title', __('admin-dashboard.banner_management'))
@section('page-title', __('admin-dashboard.banner_management'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $banners->count();
    $totalCount = method_exists($banners, 'total') ? $banners->total() : $banners->count();

    $currentBanners = $banners instanceof \Illuminate\Pagination\AbstractPaginator
        ? collect($banners->items())
        : collect($banners);

    $metrics = [
        'total' => $totalCount,
        'active' => $currentBanners->where('is_active', true)->count(),
        'inactive' => $currentBanners->where('is_active', false)->count(),
        'with_link' => $currentBanners->filter(fn ($banner) => filled($banner->link))->count(),
    ];

    $jsLabels = [
        'visible' => $text('Visible', 'ظاهر'),
        'deleteTitle' => $text('Delete banner', 'حذف البانر'),
        'deleteMessage' => $text('Are you sure you want to delete this banner?', 'هل أنت متأكد من حذف هذا البانر؟'),
    ];
@endphp

@section('page-actions')
    <a href="{{ route('admin.settings.banners.create') }}" class="btn ban-primary-btn">
        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
        {{ __('admin-dashboard.add_new_banner') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="ban-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="ban-hero">
            <div class="ban-hero-main">
                <div class="ban-hero-icon">
                    <i class="fas fa-images"></i>
                </div>

                <div class="ban-hero-copy">
                    <span class="ban-chip">
                        {{ $text('Website display assets', 'عناصر العرض بالموقع') }}
                    </span>

                    <h3>{{ __('admin-dashboard.banner_management') }}</h3>

                    <p>
                        {{ $text(
                            'Manage website and app banners, preview images, links, and activation status from one clean screen.',
                            'إدارة بانرات الموقع والتطبيق، معاينة الصور والروابط وحالة التفعيل من شاشة واحدة منظمة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="ban-total-card">
                <span>{{ $text('Total banners', 'إجمالي البانرات') }}</span>
                <strong>{{ number_format($totalCount) }}</strong>
                <small>{{ number_format($visibleCount) }} {{ $text('visible on this page', 'ظاهر في الصفحة الحالية') }}</small>
            </div>
        </section>

        <section class="ban-metrics">
            <div class="ban-metric">
                <div class="ban-metric-icon primary">
                    <i class="fas fa-images"></i>
                </div>

                <div>
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ number_format($metrics['total']) }}</strong>
                    <small>{{ $text('All banner records.', 'كل سجلات البانرات.') }}</small>
                </div>
            </div>

            <div class="ban-metric">
                <div class="ban-metric-icon success">
                    <i class="fas fa-circle-check"></i>
                </div>

                <div>
                    <span>{{ __('admin-dashboard.active') }}</span>
                    <strong>{{ number_format($metrics['active']) }}</strong>
                    <small>{{ $text('Active on current page.', 'مفعلة في الصفحة الحالية.') }}</small>
                </div>
            </div>

            <div class="ban-metric">
                <div class="ban-metric-icon muted">
                    <i class="fas fa-pause"></i>
                </div>

                <div>
                    <span>{{ __('admin-dashboard.inactive') }}</span>
                    <strong>{{ number_format($metrics['inactive']) }}</strong>
                    <small>{{ $text('Inactive on current page.', 'غير مفعلة في الصفحة الحالية.') }}</small>
                </div>
            </div>

            <div class="ban-metric">
                <div class="ban-metric-icon info">
                    <i class="fas fa-link"></i>
                </div>

                <div>
                    <span>{{ $text('With links', 'لها روابط') }}</span>
                    <strong>{{ number_format($metrics['with_link']) }}</strong>
                    <small>{{ $text('Clickable banners.', 'بانرات قابلة للضغط.') }}</small>
                </div>
            </div>
        </section>

        <section class="ban-toolbar">
            <div class="ban-search">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="bannerSearchInput"
                    placeholder="{{ $text('Search by link, status, date...', 'ابحث بالرابط أو الحالة أو التاريخ...') }}"
                >
            </div>

            <div class="ban-filters">
                <button type="button" class="ban-filter active" data-filter="all">{{ $text('All', 'الكل') }}</button>
                <button type="button" class="ban-filter" data-filter="active">{{ __('admin-dashboard.active') }}</button>
                <button type="button" class="ban-filter" data-filter="inactive">{{ __('admin-dashboard.inactive') }}</button>
                <button type="button" class="ban-filter" data-filter="with-link">{{ $text('With link', 'برابط') }}</button>
                <button type="button" class="ban-filter" data-filter="no-link">{{ $text('No link', 'بدون رابط') }}</button>
            </div>

            <div class="ban-view-toggle">
                <button type="button" class="ban-view-btn active" data-view="table">
                    <i class="fas fa-table"></i>
                </button>

                <button type="button" class="ban-view-btn" data-view="cards">
                    <i class="fas fa-grip"></i>
                </button>
            </div>
        </section>

        <section class="ban-list-card">
            <div class="ban-list-head">
                <div>
                    <h5>{{ __('admin-dashboard.all_banners') }}</h5>
                    <p>
                        {{ $text(
                            'Review images, links, activation status, and manage banner actions.',
                            'راجع الصور والروابط وحالة التفعيل وإجراءات إدارة البانرات.'
                        ) }}
                    </p>
                </div>

                <div class="ban-list-actions">
                    <span class="ban-count-pill" id="bannerVisibleCounter">
                        {{ $visibleCount }} / {{ $totalCount }}
                    </span>

                    <a href="{{ route('admin.settings.banners.create') }}" class="btn ban-primary-btn">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.add_new_banner') }}
                    </a>
                </div>
            </div>

            @if($banners->count() > 0)
                <div class="ban-table-view" id="bannerTableView">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 ban-table">
                            <thead>
                                <tr>
                                    <th>{{ __('admin-dashboard.image') }}</th>
                                    <th>{{ __('admin-dashboard.link') }}</th>
                                    <th>{{ __('admin-dashboard.status') }}</th>
                                    <th>{{ __('admin-dashboard.created_at') }}</th>
                                    <th class="text-end">{{ __('admin-dashboard.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($banners as $banner)
                                    @php
                                        $hasLink = filled($banner->link);
                                        $statusKey = $banner->is_active ? 'active' : 'inactive';
                                        $linkKey = $hasLink ? 'with-link' : 'no-link';

                                        $searchText = strtolower(collect([
                                            $banner->link,
                                            $banner->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive'),
                                            optional($banner->created_at)->format('Y-m-d'),
                                            $statusKey,
                                            $linkKey,
                                        ])->filter()->implode(' '));
                                    @endphp

                                    <tr
                                        class="ban-row"
                                        data-search="{{ $searchText }}"
                                        data-status="{{ $statusKey }}"
                                        data-link="{{ $linkKey }}"
                                    >
                                        <td>
                                            <div class="ban-preview-cell">
                                                @if($banner->image)
                                                    <img
                                                        src="{{ asset($banner->image) }}"
                                                        alt="Banner"
                                                        class="ban-image"
                                                    >
                                                @else
                                                    <div class="ban-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif

                                                <div>
                                                    <strong>{{ $text('Banner', 'بانر') }} #{{ $banner->id }}</strong>
                                                    <small>{{ $banner->image ? basename($banner->image) : __('admin-dashboard.image') }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($banner->link)
                                                <a href="{{ $banner->link }}" target="_blank" class="ban-link">
                                                    <i class="fas fa-up-right-from-square {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                                    {{ \Illuminate\Support\Str::limit($banner->link, 58) }}
                                                </a>
                                            @else
                                                <span class="ban-muted">
                                                    {{ __('admin-dashboard.no_link') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="ban-badge {{ $banner->is_active ? 'active' : 'inactive' }}">
                                                <i class="fas fa-{{ $banner->is_active ? 'circle-check' : 'pause' }}"></i>
                                                {{ $banner->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="ban-date">
                                                @include('admin.partials.date', ['date' => $banner->created_at])
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <div class="ban-actions">
                                                <form
                                                    action="{{ route('admin.settings.banners.toggle-status', $banner->id) }}"
                                                    method="POST"
                                                    class="m-0"
                                                    onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'هل تريد إيقاف هذا البانر؟' : 'هل تريد تفعيل هذا البانر؟') : ($banner->is_active ? 'Are you sure you want to deactivate this banner?' : 'Are you sure you want to activate this banner?') }}');"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="btn ban-icon-btn {{ $banner->is_active ? 'warning' : 'success' }}"
                                                        title="{{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'إيقاف' : 'تفعيل') : ($banner->is_active ? 'Deactivate' : 'Activate') }}"
                                                    >
                                                        <i class="fas fa-{{ $banner->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>

                                                <a
                                                    href="{{ route('admin.settings.banners.edit', $banner->id) }}"
                                                    class="btn ban-icon-btn edit"
                                                    title="{{ __('admin-dashboard.edit') }}"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn ban-icon-btn delete js-banner-delete-btn"
                                                    data-action="{{ route('admin.settings.banners.destroy', $banner->id) }}"
                                                    data-title="{{ $text('Banner', 'بانر') }} #{{ $banner->id }}"
                                                    data-message="{{ __('admin-dashboard.confirm_delete_banner') }}"
                                                    title="{{ __('admin-dashboard.delete') }}"
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

                <div class="ban-card-view d-none" id="bannerCardView">
                    @foreach($banners as $banner)
                        @php
                            $hasLink = filled($banner->link);
                            $statusKey = $banner->is_active ? 'active' : 'inactive';
                            $linkKey = $hasLink ? 'with-link' : 'no-link';

                            $searchText = strtolower(collect([
                                $banner->link,
                                $banner->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive'),
                                optional($banner->created_at)->format('Y-m-d'),
                                $statusKey,
                                $linkKey,
                            ])->filter()->implode(' '));
                        @endphp

                        <article
                            class="ban-card ban-row"
                            data-search="{{ $searchText }}"
                            data-status="{{ $statusKey }}"
                            data-link="{{ $linkKey }}"
                        >
                            <div class="ban-card-media">
                                @if($banner->image)
                                    <img src="{{ asset($banner->image) }}" alt="Banner">
                                @else
                                    <div class="ban-card-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif

                                <span class="ban-badge {{ $banner->is_active ? 'active' : 'inactive' }}">
                                    {{ $banner->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                </span>
                            </div>

                            <div class="ban-card-body">
                                <h5>{{ $text('Banner', 'بانر') }} #{{ $banner->id }}</h5>

                                <div class="ban-card-info">
                                    <span>{{ __('admin-dashboard.link') }}</span>

                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank">
                                            {{ \Illuminate\Support\Str::limit($banner->link, 70) }}
                                        </a>
                                    @else
                                        <strong>{{ __('admin-dashboard.no_link') }}</strong>
                                    @endif
                                </div>

                                <div class="ban-card-info">
                                    <span>{{ __('admin-dashboard.created_at') }}</span>
                                    <strong>@include('admin.partials.date', ['date' => $banner->created_at])</strong>
                                </div>

                                <div class="ban-card-actions">
                                    <form
                                        action="{{ route('admin.settings.banners.toggle-status', $banner->id) }}"
                                        method="POST"
                                        class="m-0"
                                        onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'هل تريد إيقاف هذا البانر؟' : 'هل تريد تفعيل هذا البانر؟') : ($banner->is_active ? 'Are you sure you want to deactivate this banner?' : 'Are you sure you want to activate this banner?') }}');"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn {{ $banner->is_active ? 'ban-warning-btn' : 'ban-success-btn' }}">
                                            <i class="fas fa-{{ $banner->is_active ? 'pause' : 'play' }} {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                            {{ app()->getLocale() === 'ar' ? ($banner->is_active ? 'إيقاف' : 'تفعيل') : ($banner->is_active ? 'Deactivate' : 'Activate') }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.settings.banners.edit', $banner->id) }}" class="btn ban-light-btn">
                                        <i class="fas fa-edit {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                        {{ __('admin-dashboard.edit') }}
                                    </a>

                                    <button
                                        type="button"
                                        class="btn ban-danger-btn js-banner-delete-btn"
                                        data-action="{{ route('admin.settings.banners.destroy', $banner->id) }}"
                                        data-title="{{ $text('Banner', 'بانر') }} #{{ $banner->id }}"
                                        data-message="{{ __('admin-dashboard.confirm_delete_banner') }}"
                                    >
                                        <i class="fas fa-trash {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                        {{ __('admin-dashboard.delete') }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="ban-empty-filter d-none" id="bannerEmptyFilter">
                    <i class="fas fa-search"></i>
                    <h5>{{ $text('No matching banners', 'لا توجد بانرات مطابقة') }}</h5>
                    <p>{{ $text('Try changing the search term or selected filter.', 'جرّب تغيير كلمة البحث أو الفلتر المحدد.') }}</p>
                </div>

                <div class="ban-pagination">
                    {{ $banners->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="ban-empty">
                    <div class="ban-empty-icon">
                        <i class="fas fa-images"></i>
                    </div>

                    <h5>{{ __('admin-dashboard.no_banners_found') }}</h5>
                    <p>{{ __('admin-dashboard.no_banners_message') }}</p>

                    <a href="{{ route('admin.settings.banners.create') }}" class="btn ban-primary-btn">
                        <i class="fas fa-plus {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.add_new_banner') }}
                    </a>
                </div>
            @endif
        </section>
    </div>

    <div class="modal fade" id="bannerDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ban-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">{{ $text('Confirm delete', 'تأكيد الحذف') }}</h5>
                        <p class="ban-modal-subtitle mb-0" id="bannerDeleteSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" id="bannerDeleteForm">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <div class="ban-warning-box">
                            <div>
                                <i class="fas fa-images"></i>
                            </div>

                            <p id="bannerDeleteMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn ban-light-btn" data-bs-dismiss="modal">
                            {{ $text('Cancel', 'إلغاء') }}
                        </button>

                        <button type="submit" class="btn ban-danger-btn">
                            {{ __('admin-dashboard.delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style data-page-style>
        .ban-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ban-hero,
        .ban-metric,
        .ban-toolbar,
        .ban-list-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .ban-hero {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .ban-hero::before {
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

        .ban-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .ban-hero-icon {
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

        .ban-chip {
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

        .ban-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .ban-hero-copy p {
            max-width: 850px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .ban-total-card {
            min-width: 240px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .ban-total-card span,
        .ban-metric span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .ban-total-card strong {
            display: block;
            color: #111827;
            font-size: 1.55rem;
            font-weight: 950;
            line-height: 1;
        }

        .ban-total-card small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            margin-top: .5rem;
        }

        .ban-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .ban-metric {
            min-height: 106px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: .8rem;
        }

        .ban-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ban-metric-icon.primary {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
        }

        .ban-metric-icon.success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .ban-metric-icon.muted {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #64748b;
        }

        .ban-metric-icon.info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
        }

        .ban-metric strong {
            display: block;
            color: #111827;
            font-size: 1.5rem;
            font-weight: 950;
            line-height: 1;
        }

        .ban-metric small {
            display: block;
            color: #64748b;
            font-size: .76rem;
            margin-top: .45rem;
            line-height: 1.5;
        }

        .ban-toolbar {
            padding: .9rem;
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto auto;
            gap: .75rem;
            align-items: center;
        }

        .ban-search {
            position: relative;
        }

        .ban-search i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        [dir="ltr"] .ban-search i {
            left: .9rem;
        }

        [dir="rtl"] .ban-search i {
            right: .9rem;
        }

        .ban-search input {
            width: 100%;
            min-height: 44px;
            border: 1px solid #dbe3ea;
            border-radius: 999px;
            outline: 0;
            background: #fff;
            color: #111827;
            font-size: .9rem;
            font-weight: 700;
        }

        [dir="ltr"] .ban-search input {
            padding: .65rem 1rem .65rem 2.55rem;
        }

        [dir="rtl"] .ban-search input {
            padding: .65rem 2.55rem .65rem 1rem;
        }

        .ban-search input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .ban-filters,
        .ban-view-toggle,
        .ban-list-actions {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .ban-filter,
        .ban-view-btn,
        .ban-count-pill {
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

        .ban-view-btn {
            width: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ban-filter:hover,
        .ban-filter.active,
        .ban-view-btn:hover,
        .ban-view-btn.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .ban-list-card {
            overflow: hidden;
        }

        .ban-list-head {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .ban-list-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .ban-list-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.7;
        }

        .ban-primary-btn,
        .ban-light-btn,
        .ban-danger-btn,
        .ban-success-btn,
        .ban-warning-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ban-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .ban-primary-btn:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .ban-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .ban-danger-btn {
            background: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #fff !important;
        }

        .ban-success-btn {
            background: #047857 !important;
            border-color: #047857 !important;
            color: #fff !important;
        }

        .ban-warning-btn {
            background: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #111827 !important;
        }

        .ban-table {
            min-width: 980px;
            table-layout: fixed;
        }

        .ban-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 1px solid #e5e7eb !important;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .ban-table tbody td {
            padding: 1rem;
            border-color: #eef2f7 !important;
            vertical-align: middle;
        }

        .ban-table tbody tr:hover {
            background: #fbfdff;
        }

        .ban-preview-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .ban-image {
            width: 112px;
            height: 54px;
            border-radius: 16px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .ban-placeholder {
            width: 112px;
            height: 54px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ban-preview-cell strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .ban-preview-cell small {
            display: block;
            color: #64748b;
            font-size: .74rem;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ban-link {
            display: inline-flex;
            align-items: center;
            color: #1d4ed8;
            font-size: .84rem;
            font-weight: 850;
            text-decoration: none;
            line-height: 1.6;
        }

        .ban-link:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .ban-muted,
        .ban-date {
            color: #64748b;
            font-size: .84rem;
            font-weight: 800;
        }

        .ban-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .ban-badge.active {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .ban-badge.inactive {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #64748b;
        }

        .ban-actions {
            display: flex;
            justify-content: flex-end;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .ban-icon-btn {
            width: 38px;
            height: 38px;
            padding: 0 !important;
            border-radius: 14px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ban-icon-btn.success {
            background: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            color: #047857 !important;
        }

        .ban-icon-btn.warning {
            background: #fffbeb !important;
            border: 1px solid #fde68a !important;
            color: #b45309 !important;
        }

        .ban-icon-btn.edit {
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
        }

        .ban-icon-btn.delete {
            background: #fef2f2 !important;
            border: 1px solid #fecaca !important;
            color: #b91c1c !important;
        }

        .ban-icon-btn:hover {
            filter: brightness(.96);
        }

        .ban-card-view {
            padding: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .9rem;
        }

        .ban-card {
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
            overflow: hidden;
        }

        .ban-card-media {
            height: 150px;
            background: #f8fafc;
            position: relative;
            border-bottom: 1px solid #e5e7eb;
        }

        .ban-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ban-card-placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 2rem;
            background: #eff6ff;
        }

        .ban-card-media .ban-badge {
            position: absolute;
            top: .75rem;
            inset-inline-start: .75rem;
            background: #fff;
        }

        .ban-card-body {
            padding: 1rem;
        }

        .ban-card-body h5 {
            margin: 0 0 .8rem;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .ban-card-info {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            margin-bottom: .65rem;
        }

        .ban-card-info span {
            display: block;
            color: #64748b;
            font-size: .74rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .ban-card-info a,
        .ban-card-info strong {
            display: block;
            color: #111827;
            font-size: .82rem;
            font-weight: 850;
            line-height: 1.6;
            text-decoration: none;
            word-break: break-word;
        }

        .ban-card-info a {
            color: #1d4ed8;
        }

        .ban-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: .9rem;
        }

        .ban-card-actions .btn {
            min-height: 38px;
            font-size: .78rem;
        }

        .ban-empty,
        .ban-empty-filter {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .ban-empty-icon,
        .ban-empty-filter i {
            width: 78px;
            height: 78px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
        }

        .ban-empty h5,
        .ban-empty-filter h5 {
            color: #111827;
            font-weight: 950;
            margin-bottom: .45rem;
        }

        .ban-empty p,
        .ban-empty-filter p {
            max-width: 560px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .ban-pagination {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: center;
            border-top: 1px solid #e5e7eb;
        }

        .ban-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .ban-modal .modal-title {
            color: #111827;
            font-weight: 950;
        }

        .ban-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .ban-warning-box {
            display: flex;
            align-items: flex-start;
            gap: .85rem;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
        }

        .ban-warning-box div {
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

        .ban-warning-box p {
            margin: 0;
            color: #334155;
            line-height: 1.7;
            font-weight: 800;
        }

        @media (max-width: 1199.98px) {
            .ban-hero {
                flex-direction: column;
                align-items: stretch;
            }

            .ban-total-card {
                min-width: 0;
            }

            .ban-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ban-toolbar {
                grid-template-columns: 1fr;
            }

            .ban-card-view {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .ban-hero,
            .ban-metric,
            .ban-toolbar,
            .ban-list-card {
                border-radius: 18px;
            }

            .ban-hero,
            .ban-list-head {
                padding: 1rem;
            }

            .ban-hero-main {
                flex-direction: column;
            }

            .ban-hero-copy h3 {
                font-size: 1.2rem;
            }

            .ban-metrics,
            .ban-card-view {
                grid-template-columns: 1fr;
            }

            .ban-list-head {
                flex-direction: column;
                align-items: stretch;
            }

            .ban-list-actions,
            .ban-list-actions .btn {
                width: 100%;
            }

            .ban-list-actions .btn {
                justify-content: center;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = {!! json_encode($jsLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

            const searchInput = document.getElementById('bannerSearchInput');
            const filterButtons = document.querySelectorAll('.ban-filter');
            const viewButtons = document.querySelectorAll('.ban-view-btn');
            const tableView = document.getElementById('bannerTableView');
            const cardView = document.getElementById('bannerCardView');
            const emptyFilter = document.getElementById('bannerEmptyFilter');
            const visibleCounter = document.getElementById('bannerVisibleCounter');

            let activeFilter = 'all';

            function rows() {
                return Array.from(document.querySelectorAll('.ban-row'));
            }

            function matchesFilter(row) {
                if (activeFilter === 'all') {
                    return true;
                }

                if (activeFilter === 'active' || activeFilter === 'inactive') {
                    return row.dataset.status === activeFilter;
                }

                return row.dataset.link === activeFilter;
            }

            function applyFilters() {
                const term = searchInput && searchInput.value ? searchInput.value.trim().toLowerCase() : '';
                let visible = 0;

                rows().forEach(function (row) {
                    const search = row.dataset.search || '';
                    const shouldShow = search.includes(term) && matchesFilter(row);

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

            const modalEl = document.getElementById('bannerDeleteModal');

            if (modalEl && typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                const form = document.getElementById('bannerDeleteForm');
                const subtitle = document.getElementById('bannerDeleteSubtitle');
                const message = document.getElementById('bannerDeleteMessage');

                document.addEventListener('click', function (event) {
                    const button = event.target.closest('.js-banner-delete-btn');

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
                        message.textContent = button.getAttribute('data-message') || labels.deleteMessage;
                    }

                    modal.show();
                });
            }

            applyFilters();
        });
    </script>
@endsection