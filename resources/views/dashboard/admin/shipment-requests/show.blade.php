@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل طلب الشحن' : 'Shipment Request Details')
@section('page-title', (app()->getLocale() === 'ar' ? 'تفاصيل طلب الشحن' : 'Shipment Request Details') . ' - ' . ($shipmentRequest->request_number ?? ('#' . $shipmentRequest->id)))

@php
    use Illuminate\Support\Facades\Route;

    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $safeRoute = static function (string $name, mixed $parameters = []) {
        return Route::has($name) ? route($name, $parameters) : null;
    };

    $statusValue = $shipmentRequest->status?->value ?? 'unknown';

    $statusLabel = match ($statusValue) {
        'draft' => $text('Draft', 'مسودة'),
        'submitted' => $text('Submitted', 'مُرسل'),
        default => $text(
            ucfirst(str_replace('_', ' ', $statusValue)),
            ucfirst(str_replace('_', ' ', $statusValue))
        ),
    };

    $statusTone = match ($statusValue) {
        'submitted' => 'warning',
        'draft' => 'secondary',
        default => 'secondary',
    };

    $sender = $shipmentRequest->senderContact;
    $receiver = $shipmentRequest->receiverContact;
    $senderAddress = $sender?->primaryAddress;
    $receiverAddress = $receiver?->primaryAddress;

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

    $mediaUrl = static function ($media) {
        $path = data_get($media, 'url') ?: data_get($media, 'path') ?: data_get($media, 'file_path');

        if (! $path) {
            $directory = trim((string) data_get($media, 'directory'), '/');
            $filename = trim((string) data_get($media, 'filename'));
            $path = $directory && $filename ? $directory . '/' . $filename : null;
        }

        if (! $path) {
            return null;
        }

        return preg_match('/^https?:\\/\\//i', $path) ? $path : asset($path);
    };

    $requestNumber = $shipmentRequest->request_number ?? ('#' . $shipmentRequest->id);
    $createdAt = $shipmentRequest->created_at?->format('Y-m-d H:i') ?? '--';
    $submittedAt = $shipmentRequest->submitted_at?->format('Y-m-d H:i');

    $packagesCount = $shipmentRequest->packages->count();
    $customerName = $shipmentRequest->user?->username ?? $text('Guest', 'ضيف');
    $customerPhone = $shipmentRequest->user?->phone ?? '--';
    $customerEmail = $shipmentRequest->user?->email ?? '--';
    $customerInitial = mb_substr($customerName, 0, 1);

    $routeIcon = $isArabic ? 'fa-arrow-left' : 'fa-arrow-right';

    $statusSteps = [
        'draft' => ['label' => $text('Draft', 'مسودة'), 'icon' => 'fa-pen'],
        'submitted' => ['label' => $text('Submitted', 'مُرسل'), 'icon' => 'fa-paper-plane'],
    ];

    $currentStepIndex = array_search($statusValue, array_keys($statusSteps), true);
@endphp

@section('page-actions')
    @if ($safeRoute('admin.shipment-requests.index'))
        <a href="{{ $safeRoute('admin.shipment-requests.index') }}" class="btn srd-back-btn">
            <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
            {{ $text('Back to list', 'العودة إلى القائمة') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="srd-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="srd-hero">
            <div class="srd-hero-main">
                <div class="srd-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>

                <div class="srd-hero-text">
                    <span class="srd-chip">{{ $text('Shipment request', 'طلب شحن') }}</span>

                    <h3>{{ $requestNumber }}</h3>

                    <div class="srd-meta-row">
                        <span class="srd-status srd-status-{{ $statusTone }}">
                            <i></i>
                            {{ $statusLabel }}
                        </span>

                        <span class="srd-meta-pill">
                            <i class="far fa-calendar-alt"></i>
                            {{ $createdAt }}
                        </span>

                        @if ($submittedAt)
                            <span class="srd-meta-pill">
                                <i class="fas fa-paper-plane"></i>
                                {{ $submittedAt }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="srd-hero-stats">
                <div class="srd-stat">
                    <span>{{ $text('Packages', 'الطرود') }}</span>
                    <strong>{{ $packagesCount }}</strong>
                </div>

                <div class="srd-stat">
                    <span>{{ $text('Status', 'الحالة') }}</span>
                    <strong class="srd-stat-text">{{ $statusLabel }}</strong>
                </div>
            </div>
        </section>

        <section class="srd-route-card">
            <div class="srd-section-head">
                <div>
                    <h5>{{ $text('Shipment route', 'مسار الشحنة') }}</h5>
                    <p>{{ $text('Sender and receiver route overview.', 'نظرة سريعة على المرسل والمستلم.') }}</p>
                </div>
            </div>

            <div class="srd-route">
                <div class="srd-route-point">
                    <span class="srd-route-label">{{ $text('From', 'من') }}</span>
                    <strong>{{ $sender?->full_name ?? '--' }}</strong>
                    <small>{{ $sender?->primary_mobile ?? '--' }}</small>
                    <p>{{ $locationLabel($senderAddress) }}</p>
                </div>

                <div class="srd-route-arrow">
                    <i class="fas {{ $routeIcon }}"></i>
                </div>

                <div class="srd-route-point">
                    <span class="srd-route-label">{{ $text('To', 'إلى') }}</span>
                    <strong>{{ $receiver?->full_name ?? '--' }}</strong>
                    <small>{{ $receiver?->primary_mobile ?? '--' }}</small>
                    <p>{{ $locationLabel($receiverAddress) }}</p>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <section class="srd-card h-100">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Customer', 'العميل') }}</h5>
                            <p>{{ $text('Main customer information.', 'بيانات العميل الأساسية.') }}</p>
                        </div>
                    </div>

                    <div class="srd-person">
                        <span class="srd-avatar">{{ $customerInitial }}</span>

                        <div>
                            <strong>{{ $customerName }}</strong>
                            <small>{{ $customerPhone }}</small>
                        </div>
                    </div>

                    <div class="srd-info-list">
                        <div>
                            <span>{{ $text('Phone', 'الهاتف') }}</span>
                            <strong>{{ $customerPhone }}</strong>
                        </div>

                        <div>
                            <span>{{ $text('Email', 'البريد الإلكتروني') }}</span>
                            <strong>{{ $customerEmail }}</strong>
                        </div>

                        <div>
                            <span>{{ $text('Request date', 'تاريخ الطلب') }}</span>
                            <strong>{{ $createdAt }}</strong>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="srd-card h-100">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Sender', 'المرسل') }}</h5>
                            <p>{{ $text('Pickup contact details.', 'بيانات جهة الاستلام.') }}</p>
                        </div>
                    </div>

                    @if ($sender)
                        <div class="srd-info-list">
                            <div>
                                <span>{{ $text('Full name', 'الاسم الكامل') }}</span>
                                <strong>{{ $sender->full_name ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Primary mobile', 'الهاتف الرئيسي') }}</span>
                                <strong>{{ $sender->primary_mobile ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Secondary mobile', 'هاتف إضافي') }}</span>
                                <strong>{{ $sender->secondary_mobile ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Address', 'العنوان') }}</span>
                                <strong>{{ $locationLabel($senderAddress) }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="srd-empty-note">
                            {{ $text('Sender details are not available.', 'بيانات المرسل غير متوفرة.') }}
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="srd-card h-100">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Receiver', 'المستلم') }}</h5>
                            <p>{{ $text('Delivery contact details.', 'بيانات جهة التسليم.') }}</p>
                        </div>
                    </div>

                    @if ($receiver)
                        <div class="srd-info-list">
                            <div>
                                <span>{{ $text('Full name', 'الاسم الكامل') }}</span>
                                <strong>{{ $receiver->full_name ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Primary mobile', 'الهاتف الرئيسي') }}</span>
                                <strong>{{ $receiver->primary_mobile ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Secondary mobile', 'هاتف إضافي') }}</span>
                                <strong>{{ $receiver->secondary_mobile ?? '--' }}</strong>
                            </div>

                            <div>
                                <span>{{ $text('Address', 'العنوان') }}</span>
                                <strong>{{ $locationLabel($receiverAddress) }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="srd-empty-note">
                            {{ $text('Receiver details are not available.', 'بيانات المستلم غير متوفرة.') }}
                        </div>
                    @endif
                </section>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <section class="srd-card">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Packages', 'الطرود') }}</h5>
                            <p>{{ $text('Package details, dimensions, value, notes, and images.', 'تفاصيل الطرود والأبعاد والقيمة والملاحظات والصور.') }}</p>
                        </div>

                        <span class="srd-count-pill">
                            {{ $packagesCount }}
                            {{ $text('package(s)', 'طرد') }}
                        </span>
                    </div>

                    @if ($shipmentRequest->packages->count())
                        <div class="srd-package-list">
                            @foreach ($shipmentRequest->packages as $package)
                                @php
                                    $packageMeta = $package->metadata ?? [];
                                    $packageImages = $package->relationLoaded('mediaFiles') ? $package->mediaFiles : collect();

                                    $badges = [];

                                    if (! empty(data_get($packageMeta, 'is_fragile'))) {
                                        $badges[] = $text('Fragile', 'قابل للكسر');
                                    }

                                    if (! empty(data_get($packageMeta, 'needs_cooling'))) {
                                        $badges[] = $text('Cooling', 'تبريد');
                                    }

                                    if (! empty(data_get($packageMeta, 'is_valuable'))) {
                                        $badges[] = $text('Valuable', 'ثمين');
                                    }

                                    if (! empty(data_get($packageMeta, 'is_documents'))) {
                                        $badges[] = $text('Documents', 'مستندات');
                                    }
                                @endphp

                                <article class="srd-package">
                                    <div class="srd-package-top">
                                        <div>
                                            <h6>{{ $package->package_name ?? $text('Package', 'طرد') . ' #' . $loop->iteration }}</h6>
                                            <p>{{ $package->package_type ?? $text('No type provided', 'لا يوجد نوع') }}</p>
                                        </div>

                                        <span class="srd-count-pill">
                                            {{ $package->quantity ?? 1 }}
                                            {{ $text('item(s)', 'عنصر') }}
                                        </span>
                                    </div>

                                    <div class="srd-package-grid">
                                        <div>
                                            <span>{{ $text('Weight', 'الوزن') }}</span>
                                            <strong>{{ $package->weight ?? '--' }}</strong>
                                        </div>

                                        <div>
                                            <span>{{ $text('Dimensions', 'الأبعاد') }}</span>
                                            <strong>{{ $package->length ?? '--' }} × {{ $package->width ?? '--' }} × {{ $package->height ?? '--' }}</strong>
                                        </div>

                                        <div>
                                            <span>{{ $text('Declared value', 'القيمة المعلنة') }}</span>
                                            <strong>{{ $package->declared_value ?? '--' }}</strong>
                                        </div>
                                    </div>

                                    @if ($badges)
                                        <div class="srd-badges">
                                            @foreach ($badges as $badge)
                                                <span>{{ $badge }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="srd-package-note">
                                        <span>{{ $text('Notes', 'ملاحظات') }}</span>
                                        <p>{{ $package->notes ?: $text('No package notes.', 'لا توجد ملاحظات على الطرد.') }}</p>
                                    </div>

                                    @if ($packageImages->count())
                                        <div class="srd-images">
                                            <span>{{ $text('Attached images', 'الصور المرفقة') }}</span>

                                            <div>
                                                @foreach ($packageImages as $media)
                                                    @php $url = $mediaUrl($media); @endphp

                                                    @if ($url)
                                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="srd-image-link">
                                                            <img src="{{ $url }}" alt="{{ $package->package_name ?? $text('Package image', 'صورة الطرد') }}">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="srd-empty-note">
                            {{ $text('No package records were saved for this request.', 'لم يتم حفظ أي طرود لهذا الطلب.') }}
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <section class="srd-card mb-4">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Status timeline', 'الجدول الزمني') }}</h5>
                            <p>{{ $text('Current request progress.', 'تقدم الطلب الحالي.') }}</p>
                        </div>
                    </div>

                    <div class="srd-timeline">
                        @foreach ($statusSteps as $key => $step)
                            @php
                                $stepIndex = array_search($key, array_keys($statusSteps), true);
                                $isActive = $currentStepIndex !== false && $stepIndex <= $currentStepIndex;
                                $isCurrent = $currentStepIndex !== false && $stepIndex === $currentStepIndex;
                            @endphp

                            <div class="srd-timeline-row {{ $isActive ? 'is-active' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                                <div class="srd-timeline-icon">
                                    <i class="fas {{ $step['icon'] }}"></i>
                                </div>

                                <div>
                                    <strong>{{ $step['label'] }}</strong>
                                    <small>
                                        {{ $isCurrent ? $text('Current step', 'المرحلة الحالية') : ($isActive ? $text('Completed', 'مكتمل') : $text('Pending', 'قيد الانتظار')) }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="srd-helper-note">
                        {{ $text(
                            'Only the current status is stored, so this timeline shows the available draft/submitted flow.',
                            'الحالة الحالية فقط هي المخزنة، لذلك يعرض المخطط التدفق المتاح: مسودة / مُرسل.'
                        ) }}
                    </div>
                </section>

                <section class="srd-card">
                    <div class="srd-section-head">
                        <div>
                            <h5>{{ $text('Company assignment', 'تعيين شركة الشحن') }}</h5>
                            <p>{{ $text('Assignment status.', 'حالة التعيين.') }}</p>
                        </div>
                    </div>

                    <div class="srd-empty-note">
                        {{ $text(
                            'No shipment company relation is stored yet, so this request is currently shown as unassigned.',
                            'لا توجد علاقة مخزنة لشركة الشحن بعد، لذلك يظهر هذا الطلب كغير مُعيّن حالياً.'
                        ) }}
                    </div>
                </section>
            </div>
        </div>

        <section class="srd-card">
            <div class="srd-section-head">
                <div>
                    <h5>{{ $text('Notes', 'الملاحظات') }}</h5>
                    <p>{{ $text('Request notes and package reasons.', 'ملاحظات الطلب وأسباب الطرود.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="srd-note-box">
                        <span>{{ $text('Request notes', 'ملاحظات الطلب') }}</span>
                        <p>
                            {{ $shipmentRequest->notes ?: $text('No request notes were provided.', 'لم يتم تقديم ملاحظات للطلب.') }}
                        </p>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="srd-note-box">
                        <span>{{ $text('Package notes / reasons', 'ملاحظات الطرود / الأسباب') }}</span>

                        @if ($shipmentRequest->packages->count())
                            <ul>
                                @foreach ($shipmentRequest->packages as $package)
                                    <li>
                                        <strong>{{ $package->package_name ?? ($text('Package', 'طرد') . ' #' . $loop->iteration) }}:</strong>
                                        {{ $package->notes ?: data_get($package->metadata, 'reason') ?: $text('No package reason stored.', 'لا يوجد سبب مخزن للطرد.') }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $text('No package reasons are available.', 'لا توجد أسباب متاحة للطرد.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .srd-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .srd-back-btn {
            min-height: 44px;
            padding: .55rem 1rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #1f2937;
            font-weight: 800;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .04);
        }

        .srd-back-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .srd-hero,
        .srd-route-card,
        .srd-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .srd-hero {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .srd-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .srd-icon {
            width: 56px;
            height: 56px;
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

        .srd-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .7rem;
            margin-bottom: .45rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 900;
        }

        .srd-hero-text h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .srd-meta-row {
            margin-top: .7rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
        }

        .srd-meta-pill,
        .srd-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            padding: .42rem .75rem;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-size: .78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .srd-status i {
            width: .45rem;
            height: .45rem;
            border-radius: 999px;
            background: currentColor;
        }

        .srd-status-warning {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        .srd-status-secondary {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }

        .srd-hero-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(120px, 1fr));
            gap: .75rem;
            flex-shrink: 0;
        }

        .srd-stat {
            min-height: 74px;
            padding: .9rem 1rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .srd-stat span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
            margin-bottom: .35rem;
        }

        .srd-stat strong {
            color: #111827;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
        }

        .srd-stat-text {
            font-size: .95rem !important;
            line-height: 1.3 !important;
        }

        .srd-route-card,
        .srd-card {
            padding: 1.25rem;
        }

        .srd-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .srd-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .srd-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .srd-route {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 42px minmax(0, 1fr);
            align-items: stretch;
            gap: .75rem;
        }

        .srd-route-point {
            min-width: 0;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .srd-route-label {
            display: inline-flex;
            margin-bottom: .4rem;
            padding: .2rem .55rem;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            font-size: .72rem;
            font-weight: 900;
        }

        .srd-route-point strong {
            display: block;
            color: #111827;
            font-size: .98rem;
            font-weight: 900;
            line-height: 1.5;
        }

        .srd-route-point small {
            display: block;
            color: #64748b;
            font-size: .8rem;
            margin-top: .1rem;
        }

        .srd-route-point p {
            margin: .65rem 0 0;
            color: #334155;
            font-size: .85rem;
            line-height: 1.7;
        }

        .srd-route-arrow {
            width: 42px;
            min-height: 42px;
            align-self: center;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .srd-person {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .85rem;
            margin-bottom: 1rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .srd-avatar {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .srd-person strong {
            display: block;
            color: #111827;
            font-weight: 900;
        }

        .srd-person small {
            color: #64748b;
            font-size: .8rem;
        }

        .srd-info-list {
            display: grid;
            gap: .7rem;
        }

        .srd-info-list div {
            padding: .8rem .9rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .srd-info-list span {
            display: block;
            margin-bottom: .25rem;
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
        }

        .srd-info-list strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 800;
            line-height: 1.6;
            word-break: break-word;
        }

        .srd-count-pill {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .38rem .75rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .78rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .srd-package-list {
            display: grid;
            gap: 1rem;
        }

        .srd-package {
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            background: #fcfdff;
        }

        .srd-package-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .9rem;
        }

        .srd-package-top h6 {
            margin: 0;
            color: #111827;
            font-size: .98rem;
            font-weight: 900;
        }

        .srd-package-top p {
            margin: .2rem 0 0;
            color: #64748b;
            font-size: .82rem;
        }

        .srd-package-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        .srd-package-grid div,
        .srd-package-note {
            padding: .8rem .9rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .srd-package-grid span,
        .srd-package-note span,
        .srd-images > span,
        .srd-note-box > span {
            display: block;
            margin-bottom: .25rem;
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
        }

        .srd-package-grid strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 900;
            line-height: 1.5;
            word-break: break-word;
        }

        .srd-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: .8rem;
        }

        .srd-badges span {
            display: inline-flex;
            padding: .3rem .65rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #1d4ed8;
            font-size: .75rem;
            font-weight: 900;
        }

        .srd-package-note {
            margin-top: .8rem;
        }

        .srd-package-note p {
            margin: 0;
            color: #111827;
            font-size: .88rem;
            font-weight: 700;
            line-height: 1.7;
        }

        .srd-images {
            margin-top: .9rem;
        }

        .srd-images div {
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
        }

        .srd-image-link img {
            width: 68px;
            height: 68px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .srd-timeline {
            display: grid;
            gap: .75rem;
        }

        .srd-timeline-row {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .85rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .srd-timeline-row.is-active {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .srd-timeline-row.is-current {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .srd-timeline-icon {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .srd-timeline-row.is-active .srd-timeline-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .srd-timeline-row.is-current .srd-timeline-icon {
            background: #2563eb;
            color: #fff;
        }

        .srd-timeline-row strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 900;
        }

        .srd-timeline-row small {
            color: #64748b;
            font-size: .78rem;
        }

        .srd-helper-note,
        .srd-empty-note {
            margin-top: 1rem;
            padding: .9rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #475569;
            font-size: .85rem;
            line-height: 1.7;
        }

        .srd-empty-note {
            margin-top: 0;
        }

        .srd-note-box {
            height: 100%;
            padding: 1rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .srd-note-box p {
            margin: 0;
            color: #111827;
            font-size: .9rem;
            font-weight: 700;
            line-height: 1.8;
        }

        .srd-note-box ul {
            margin: 0;
            padding-inline-start: 1.1rem;
            color: #111827;
            font-size: .88rem;
            line-height: 1.8;
        }

        .srd-note-box li + li {
            margin-top: .35rem;
        }

        @media (max-width: 1199.98px) {
            .srd-hero {
                align-items: stretch;
                flex-direction: column;
            }

            .srd-hero-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .srd-hero,
            .srd-route-card,
            .srd-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .srd-hero-main {
                flex-direction: column;
            }

            .srd-hero-text h3 {
                font-size: 1.2rem;
            }

            .srd-route {
                grid-template-columns: 1fr;
            }

            .srd-route-arrow {
                margin-inline: auto;
                transform: rotate(90deg);
            }

            .srd-package-top,
            .srd-section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .srd-package-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection