@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'طلبات الشحن' : 'Shipment Requests')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة طلبات الشحن' : 'Shipment Requests Management')

@php
    use Illuminate\Support\Facades\Route;

    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $statusLabel = static fn ($status) => match ((string) $status) {
        'draft' => $text('Draft', 'مسودة'),
        'submitted' => $text('Submitted', 'مُرسل'),
        default => $text(
            ucfirst(str_replace('_', ' ', (string) $status)),
            ucfirst(str_replace('_', ' ', (string) $status))
        ),
    };

    $statusTone = static fn ($status) => match ((string) $status) {
        'submitted' => 'warning',
        'draft' => 'secondary',
        default => 'secondary',
    };

    $safeRoute = static function (string $name, mixed $parameters = []) {
        return Route::has($name) ? route($name, $parameters) : null;
    };

    $locationLabel = static function ($address) use ($text) {
        if (! $address) {
            return $text('Not provided', 'غير متوفر');
        }

        $parts = array_filter([
            data_get($address, 'city.name'),
            data_get($address, 'governorate.name'),
            data_get($address, 'state.name'),
            data_get($address, 'zone.name'),
            data_get($address, 'address_line_1'),
            data_get($address, 'address_line_2'),
            data_get($address, 'landmark'),
        ]);

        return ! empty($parts) ? implode(' · ', $parts) : $text('Not provided', 'غير متوفر');
    };

    $activeFilters = collect([
        'search' => request('search'),
        'status' => request('status'),
        'from_location' => request('from_location'),
        'to_location' => request('to_location'),
        'date_from' => request('date_from'),
        'date_to' => request('date_to'),
    ])->filter(fn ($value) => filled($value));

    $hasFilters = $activeFilters->isNotEmpty();

    $visibleCount = $requests->count();
    $totalCount = method_exists($requests, 'total') ? $requests->total() : $requests->count();
    $submittedCount = $requests->filter(fn ($request) => $request->status?->value === 'submitted')->count();
    $draftCount = $requests->filter(fn ($request) => $request->status?->value === 'draft')->count();

    $routeIcon = $isArabic ? 'fa-arrow-left' : 'fa-arrow-right';
@endphp

@section('page-actions')
    @if (Route::has('admin.dashboard'))
        <a href="{{ route('admin.dashboard') }}" class="btn sr-back-btn">
            <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
            {{ $text('Back to dashboard', 'العودة إلى لوحة التحكم') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="sr-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="sr-header-card">
            <div class="sr-header-main">
                <div class="sr-header-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>

                <div class="sr-header-text">
                    <span class="sr-chip">
                        {{ $text('Shipment requests', 'طلبات الشحن') }}
                    </span>

                    <h4>{{ $text('Manage shipment requests', 'إدارة طلبات الشحن') }}</h4>

                    <p>
                        {{ $text(
                            'Search, review, and open shipment requests from a simple focused screen.',
                            'ابحث وراجع وافتح طلبات الشحن من شاشة بسيطة وواضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="sr-metrics">
                <div class="sr-metric-item">
                    <span>{{ $text('Visible', 'المعروض') }}</span>
                    <strong>{{ $visibleCount }}</strong>
                </div>

                <div class="sr-metric-item">
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ $totalCount }}</strong>
                </div>

                <div class="sr-metric-item">
                    <span>{{ $text('Submitted', 'المُرسل') }}</span>
                    <strong>{{ $submittedCount }}</strong>
                </div>

                <div class="sr-metric-item">
                    <span>{{ $text('Draft', 'مسودة') }}</span>
                    <strong>{{ $draftCount }}</strong>
                </div>
            </div>
        </section>

        <section class="sr-filter-card">
            <div class="sr-section-head">
                <div>
                    <h5>{{ $text('Filters', 'الفلاتر') }}</h5>
                    <p>{{ $text('Use search, status, route, or date to find requests faster.', 'استخدم البحث أو الحالة أو المسار أو التاريخ للوصول للطلبات بسرعة.') }}</p>
                </div>

                @if ($hasFilters)
                    <a href="{{ $safeRoute('admin.shipment-requests.index') ?? url()->current() }}" class="btn btn-sm sr-clear-btn">
                        <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Clear filters', 'مسح الفلاتر') }}
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ $safeRoute('admin.shipment-requests.index') ?? url()->current() }}">
                <div class="row g-3">
                    <div class="col-12 col-xl-3 col-lg-4">
                        <label class="sr-label">{{ $text('Search', 'بحث') }}</label>
                        <div class="sr-input-icon">
                            <i class="fas fa-search"></i>
                            <input
                                type="text"
                                name="search"
                                class="form-control sr-control"
                                value="{{ request('search') }}"
                                placeholder="{{ $text('Request number, customer, sender, receiver, package', 'رقم الطلب، العميل، المرسل، المستلم، الطرد') }}"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-xl-2 col-lg-4">
                        <label class="sr-label">{{ $text('Status', 'الحالة') }}</label>
                        <select name="status" class="form-select sr-control">
                            <option value="">{{ $text('All statuses', 'كل الحالات') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ $statusLabel($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-xl-2 col-lg-4">
                        <label class="sr-label">{{ $text('From location', 'من الموقع') }}</label>
                        <input
                            type="text"
                            name="from_location"
                            class="form-control sr-control"
                            value="{{ request('from_location') }}"
                            placeholder="{{ $text('Sender city or address', 'مدينة المرسل أو عنوانه') }}"
                        >
                    </div>

                    <div class="col-12 col-xl-2 col-lg-4">
                        <label class="sr-label">{{ $text('To location', 'إلى الموقع') }}</label>
                        <input
                            type="text"
                            name="to_location"
                            class="form-control sr-control"
                            value="{{ request('to_location') }}"
                            placeholder="{{ $text('Receiver city or address', 'مدينة المستلم أو عنوانه') }}"
                        >
                    </div>

                    <div class="col-6 col-xl-1 col-lg-4">
                        <label class="sr-label">{{ $text('From date', 'من تاريخ') }}</label>
                        <input
                            type="date"
                            name="date_from"
                            class="form-control sr-control"
                            value="{{ request('date_from') }}"
                        >
                    </div>

                    <div class="col-6 col-xl-1 col-lg-4">
                        <label class="sr-label">{{ $text('To date', 'إلى تاريخ') }}</label>
                        <input
                            type="date"
                            name="date_to"
                            class="form-control sr-control"
                            value="{{ request('date_to') }}"
                        >
                    </div>

                    <div class="col-12 col-xl-1 col-lg-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary sr-submit-btn w-100">
                            {{ $text('Filter', 'تصفية') }}
                        </button>
                    </div>
                </div>

                @if ($hasFilters)
                    <div class="sr-active-filters">
                        <span class="sr-active-title">{{ $text('Active filters', 'الفلاتر الحالية') }}</span>

                        @foreach ($activeFilters as $key => $value)
                            <span class="sr-active-chip">
                                {{ $text(str_replace('_', ' ', ucfirst($key)), str_replace('_', ' ', ucfirst($key))) }}:
                                <strong>{{ $key === 'status' ? $statusLabel($value) : $value }}</strong>
                            </span>
                        @endforeach
                    </div>
                @endif
            </form>
        </section>

        <section class="sr-table-card">
            <div class="sr-section-head sr-table-head">
                <div>
                    <h5>{{ $text('Requests list', 'قائمة الطلبات') }}</h5>
                    <p>{{ $text('Only the important request information is shown here.', 'يتم عرض أهم بيانات الطلب فقط هنا.') }}</p>
                </div>

                <span class="sr-page-count">
                    {{ $visibleCount }} {{ $text('shown', 'معروض') }}
                </span>
            </div>

            @if ($requests->count())
                <div class="table-responsive sr-table-wrap">
                    <table class="table align-middle mb-0 sr-table">
                        <thead>
                            <tr>
                                <th class="sr-col-request">{{ $text('Request / customer', 'الطلب / العميل') }}</th>
                                <th class="sr-col-route">{{ $text('Route', 'المسار') }}</th>
                                <th class="sr-col-package">{{ $text('Package', 'الطرد') }}</th>
                                <th class="sr-col-status">{{ $text('Status', 'الحالة') }}</th>
                                <th class="sr-col-action text-end">{{ $text('Action', 'الإجراء') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($requests as $shipmentRequest)
                                @php
                                    $sender = $shipmentRequest->senderContact;
                                    $receiver = $shipmentRequest->receiverContact;
                                    $senderAddress = $sender?->primaryAddress;
                                    $receiverAddress = $receiver?->primaryAddress;

                                    $firstPackage = $shipmentRequest->relationLoaded('packages')
                                        ? $shipmentRequest->packages->first()
                                        : null;

                                    $packageLabel = $firstPackage?->package_name
                                        ?? ($shipmentRequest->packages_count > 0
                                            ? $shipmentRequest->packages_count . ' ' . $text('packages', 'طرود')
                                            : $text('No packages', 'لا توجد طرود'));

                                    $showUrl = $safeRoute('admin.shipment-requests.show', $shipmentRequest->id);

                                    $requestDate = $shipmentRequest->submitted_at?->format('Y-m-d H:i')
                                        ?? $shipmentRequest->created_at?->format('Y-m-d H:i')
                                        ?? '--';

                                    $customerName = $shipmentRequest->user?->username ?? $text('Guest', 'ضيف');
                                    $customerContact = $shipmentRequest->user?->phone ?? $shipmentRequest->user?->email ?? '--';
                                    $customerInitial = mb_substr($customerName, 0, 1);
                                @endphp

                                <tr>
                                    <td>
                                        <div class="sr-request-cell">
                                            <span class="sr-avatar">{{ $customerInitial }}</span>

                                            <div class="sr-request-info">
                                                <div class="sr-request-no">
                                                    {{ $shipmentRequest->request_number ?? ('#' . $shipmentRequest->id) }}
                                                </div>

                                                <div class="sr-customer-name">{{ $customerName }}</div>

                                                <div class="sr-muted">
                                                    {{ $customerContact }}
                                                    <span class="sr-dot-separator">•</span>
                                                    {{ $requestDate }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="sr-route-box">
                                            <div class="sr-route-person">
                                                <span class="sr-route-label">{{ $text('From', 'من') }}</span>
                                                <strong>{{ $sender?->full_name ?? '--' }}</strong>
                                                <small>{{ $locationLabel($senderAddress) }}</small>
                                            </div>

                                            <div class="sr-route-arrow">
                                                <i class="fas {{ $routeIcon }}"></i>
                                            </div>

                                            <div class="sr-route-person">
                                                <span class="sr-route-label">{{ $text('To', 'إلى') }}</span>
                                                <strong>{{ $receiver?->full_name ?? '--' }}</strong>
                                                <small>{{ $locationLabel($receiverAddress) }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="sr-package-name">{{ $packageLabel }}</div>
                                        <div class="sr-muted">
                                            {{ $shipmentRequest->packages_count ?? 0 }}
                                            {{ $text('items', 'عنصر') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="sr-status sr-status-{{ $statusTone($shipmentRequest->status?->value) }}">
                                            <i></i>
                                            {{ $statusLabel($shipmentRequest->status?->value) }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        @if ($showUrl)
                                            <a href="{{ $showUrl }}" class="btn btn-sm sr-view-btn">
                                                <i class="fas fa-eye {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                                {{ $text('View', 'عرض') }}
                                            </a>
                                        @else
                                            <span class="sr-muted">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="sr-pagination">
                    <x-pagination :paginator="$requests" />
                </div>
            @else
                <div class="sr-empty">
                    <div class="sr-empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>

                    <h5>{{ $text('No shipment requests found', 'لا توجد طلبات شحن') }}</h5>

                    <p>
                        {{ $hasFilters
                            ? $text('No requests match the selected filters. Try clearing filters or using different search terms.', 'لا توجد طلبات مطابقة للفلاتر الحالية. جرّب مسح الفلاتر أو استخدام كلمات بحث مختلفة.')
                            : $text('Shipment requests will appear here once customers submit them.', 'ستظهر طلبات الشحن هنا عند إرسالها من العملاء.')
                        }}
                    </p>

                    @if ($hasFilters)
                        <a href="{{ $safeRoute('admin.shipment-requests.index') ?? url()->current() }}" class="btn sr-view-btn">
                            <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                            {{ $text('Clear filters', 'مسح الفلاتر') }}
                        </a>
                    @endif
                </div>
            @endif
        </section>
    </div>

    <style>
        .sr-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sr-back-btn {
            min-height: 44px;
            padding: .55rem 1rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #1f2937;
            font-weight: 700;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .04);
        }

        .sr-back-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .sr-header-card,
        .sr-filter-card,
        .sr-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .sr-header-card {
            padding: 1.25rem;
        }

        .sr-header-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .sr-header-icon {
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

        .sr-chip {
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

        .sr-header-text h4 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .sr-header-text p {
            margin: .45rem 0 0;
            max-width: 820px;
            color: #64748b;
            font-size: .95rem;
            line-height: 1.8;
        }

        .sr-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1.15rem;
        }

        .sr-metric-item {
            min-height: 74px;
            padding: .85rem 1rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sr-metric-item span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            margin-bottom: .35rem;
        }

        .sr-metric-item strong {
            color: #111827;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
        }

        .sr-filter-card {
            padding: 1.25rem;
        }

        .sr-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .sr-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .sr-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .88rem;
        }

        .sr-clear-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-weight: 700;
        }

        .sr-label {
            display: block;
            margin-bottom: .4rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
        }

        .sr-control {
            min-height: 44px;
            border-radius: 13px;
            border-color: #dbe3ea;
            color: #111827;
            font-size: .9rem;
            box-shadow: none;
        }

        .sr-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .sr-input-icon {
            position: relative;
        }

        .sr-input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        [dir="ltr"] .sr-input-icon i {
            left: .85rem;
        }

        [dir="rtl"] .sr-input-icon i {
            right: .85rem;
        }

        [dir="ltr"] .sr-input-icon .sr-control {
            padding-left: 2.5rem;
        }

        [dir="rtl"] .sr-input-icon .sr-control {
            padding-right: 2.5rem;
        }

        .sr-submit-btn {
            min-height: 44px;
            border-radius: 13px;
            font-weight: 800;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .12);
        }

        .sr-active-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
            margin-top: .9rem;
        }

        .sr-active-title {
            color: #64748b;
            font-size: .8rem;
            font-weight: 800;
        }

        .sr-active-chip {
            display: inline-flex;
            gap: .25rem;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #1d4ed8;
            font-size: .78rem;
            font-weight: 700;
        }

        .sr-table-card {
            overflow: hidden;
        }

        .sr-table-head {
            padding: 1.25rem 1.25rem 0;
        }

        .sr-page-count {
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

        .sr-table-wrap {
            border-top: 1px solid #e5e7eb;
        }

        .sr-table {
            min-width: 880px;
            table-layout: fixed;
        }

        .sr-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .sr-table tbody td {
            padding: 1rem;
            border-color: #eef2f7;
            vertical-align: middle;
        }

        .sr-table tbody tr:hover {
            background: #fbfdff;
        }

        .sr-col-request {
            width: 28%;
        }

        .sr-col-route {
            width: 38%;
        }

        .sr-col-package {
            width: 14%;
        }

        .sr-col-status {
            width: 10%;
        }

        .sr-col-action {
            width: 10%;
        }

        .sr-request-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .sr-avatar {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .sr-request-info {
            min-width: 0;
        }

        .sr-request-no {
            color: #111827;
            font-weight: 900;
            line-height: 1.4;
        }

        .sr-customer-name {
            color: #111827;
            font-weight: 700;
            font-size: .9rem;
            line-height: 1.5;
        }

        .sr-muted {
            color: #64748b;
            font-size: .78rem;
            line-height: 1.5;
        }

        .sr-dot-separator {
            color: #cbd5e1;
            padding-inline: .3rem;
        }

        .sr-route-box {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 28px minmax(0, 1fr);
            align-items: center;
            gap: .55rem;
        }

        .sr-route-person {
            min-width: 0;
            padding: .65rem .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .sr-route-label {
            display: inline-block;
            margin-bottom: .2rem;
            color: #2563eb;
            font-size: .7rem;
            font-weight: 900;
        }

        .sr-route-person strong {
            display: block;
            color: #111827;
            font-size: .86rem;
            font-weight: 900;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sr-route-person small {
            display: block;
            color: #64748b;
            font-size: .75rem;
            line-height: 1.5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sr-route-arrow {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
        }

        .sr-package-name {
            color: #111827;
            font-size: .9rem;
            font-weight: 900;
            line-height: 1.5;
        }

        .sr-status {
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

        .sr-status i {
            width: .45rem;
            height: .45rem;
            border-radius: 999px;
            background: currentColor;
        }

        .sr-status-warning {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        .sr-status-secondary {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }

        .sr-view-btn {
            border-radius: 999px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            padding-inline: .9rem;
        }

        .sr-view-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .sr-pagination {
            padding: 1rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        .sr-empty {
            padding: 4rem 1.5rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .sr-empty-icon {
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

        .sr-empty h5 {
            color: #111827;
            font-weight: 900;
            margin-bottom: .45rem;
        }

        .sr-empty p {
            max-width: 520px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        @media (max-width: 1199.98px) {
            .sr-table {
                min-width: 820px;
            }
        }

        @media (max-width: 767.98px) {
            .sr-header-card,
            .sr-filter-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .sr-header-main {
                flex-direction: column;
            }

            .sr-header-text h4 {
                font-size: 1.2rem;
            }

            .sr-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sr-section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .sr-table-head {
                padding: 1rem 1rem 0;
            }

            .sr-table {
                min-width: 780px;
            }
        }
    </style>
@endsection