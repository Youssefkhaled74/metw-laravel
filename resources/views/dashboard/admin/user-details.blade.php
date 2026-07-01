@extends('layouts.admin')

@section('title', __('admin-dashboard.users_management'))
@section('page-title', __('admin-dashboard.users_management'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $displayName = $user->name ?: ($user->username ?: $text('User', 'مستخدم'));
    $initial = mb_substr($displayName, 0, 1);

    $addressesCount = $user->addresses ? $user->addresses->count() : 0;
    $defaultAddress = $user->addresses ? $user->addresses->firstWhere('is_default', true) : null;

    $profileScoreChecks = [
        filled($user->name),
        filled($user->username),
        filled($user->email),
        ! empty($user->email_verified_at),
        filled($user->phone),
        ! empty($user->phone_verified_at),
        $addressesCount > 0,
    ];

    $profileScore = (int) round((collect($profileScoreChecks)->filter()->count() / count($profileScoreChecks)) * 100);

    $statusBadges = [
        [
            'label' => $user->phone_verified_at ? __('admin-dashboard.phone_verified') : __('admin-dashboard.phone_not_verified'),
            'tone' => $user->phone_verified_at ? 'success' : 'danger',
            'icon' => $user->phone_verified_at ? 'fa-check-circle' : 'fa-times-circle',
        ],
    ];

    if ($user->email_verified_at) {
        $statusBadges[] = [
            'label' => __('admin-dashboard.email_verified'),
            'tone' => 'success',
            'icon' => 'fa-envelope-circle-check',
        ];
    } else {
        $statusBadges[] = [
            'label' => $text('Email not verified', 'البريد غير موثق'),
            'tone' => 'danger',
            'icon' => 'fa-envelope',
        ];
    }

    $addressLabel = static function ($address) use ($locale, $text) {
        if (! $address) {
            return $text('Not provided', 'غير متوفر');
        }

        $parts = array_filter([
            $address->street_name,
            optional($address->zone)->{"name_{$locale}"} ?? optional($address->zone)->name_en,
            optional($address->city)->{"name_{$locale}"} ?? optional($address->city)->name_en,
            optional($address->state)->{"name_{$locale}"} ?? optional($address->state)->name_en,
            $address->landmark,
        ]);

        return ! empty($parts) ? implode(' · ', $parts) : $text('Not provided', 'غير متوفر');
    };

    $addressTypeLabel = static fn ($type) => match ((string) $type) {
        'home' => $text('Home', 'المنزل'),
        'work' => $text('Work', 'العمل'),
        default => $text(ucfirst((string) $type), ucfirst((string) $type)),
    };

    $addressTypeIcon = static fn ($type) => match ((string) $type) {
        'home' => 'fa-home',
        'work' => 'fa-briefcase',
        default => 'fa-map-marker-alt',
    };

    $addressTypeTone = static fn ($type) => match ((string) $type) {
        'home' => 'success',
        'work' => 'primary',
        default => 'secondary',
    };
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.users') }}">{{ __('admin-dashboard.users') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.user_details') }}</li>
@endsection

@section('content')
    <div class="usd-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="usd-hero-card">
            <div class="usd-hero-main">
                @if($user->avatar)
                    <img src="{{ asset($user->avatar) }}" alt="{{ $displayName }}" class="usd-hero-avatar-img">
                @else
                    <span class="usd-hero-avatar">{{ $initial }}</span>
                @endif

                <div class="usd-hero-text">
                    <span class="usd-chip">
                        {{ __('admin-dashboard.user_details') }}
                    </span>

                    <h3>{{ $displayName }}</h3>

                    <div class="usd-meta-row">
                        <span class="usd-meta-pill">
                            <i class="fas fa-hashtag"></i>
                            ID: {{ $user->id }}
                        </span>

                        @if($user->user_number)
                            <span class="usd-meta-pill">
                                <i class="fas fa-id-card"></i>
                                {{ $user->user_number }}
                            </span>
                        @endif

                        @if($user->username)
                            <span class="usd-meta-pill">
                                <i class="fas fa-at"></i>
                                {{ $user->username }}
                            </span>
                        @endif
                    </div>

                    <div class="usd-badges-row">
                        @foreach($statusBadges as $badge)
                            <span class="usd-badge usd-badge-{{ $badge['tone'] }}">
                                <i class="fas {{ $badge['icon'] }}"></i>
                                {{ $badge['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="usd-hero-side">
                <div class="usd-score-card">
                    <div class="usd-score-top">
                        <span>{{ $text('Profile completeness', 'اكتمال الملف') }}</span>
                        <strong>{{ $profileScore }}%</strong>
                    </div>

                    <div class="usd-score-bar">
                        <span style="width: {{ $profileScore }}%"></span>
                    </div>
                </div>

                <a href="{{ route('admin.users') }}" class="btn usd-back-btn">
                    <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
                    {{ __('admin-dashboard.back_to_list') }}
                </a>
            </div>
        </section>

        <section class="usd-metrics">
            <div class="usd-metric-item">
                <span>{{ __('admin-dashboard.shipments') }}</span>
                <strong>{{ $user->orders_count }}</strong>
            </div>

            <div class="usd-metric-item">
                <span>{{ __('admin-dashboard.ecommerce_orders') }}</span>
                <strong>{{ $user->ecommerce_orders_count }}</strong>
            </div>

            <div class="usd-metric-item">
                <span>{{ __('admin-dashboard.addresses') }}</span>
                <strong>{{ $addressesCount }}</strong>
            </div>

            <div class="usd-metric-item">
                <span>{{ __('admin-dashboard.join_date') }}</span>
                <strong class="usd-metric-date">{{ $user->created_at->format('M d, Y') }}</strong>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <section class="usd-card h-100">
                    <div class="usd-section-head">
                        <div>
                            <h5>{{ $text('Profile overview', 'ملخص المستخدم') }}</h5>
                            <p>{{ $text('Basic account preferences and status.', 'بيانات الحساب والتفضيلات الأساسية.') }}</p>
                        </div>
                    </div>

                    <div class="usd-profile-box">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="{{ $displayName }}">
                        @else
                            <span>{{ $initial }}</span>
                        @endif

                        <div>
                            <strong>{{ $displayName }}</strong>

                            @if($user->username)
                                <small>
                                    <i class="fas fa-at"></i>
                                    {{ $user->username }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="usd-info-list">
                        <div>
                            <span>{{ __('admin-dashboard.join_date') }}</span>
                            <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </div>

                        @if($user->default_lang)
                            <div>
                                <span>{{ __('admin-dashboard.default_language') }}</span>
                                <strong>{{ strtoupper($user->default_lang) }}</strong>
                            </div>
                        @endif

                        @if($user->notifications_enabled !== null)
                            <div>
                                <span>{{ __('admin-dashboard.notifications') }}</span>

                                <strong class="{{ $user->notifications_enabled ? 'usd-text-success' : 'usd-text-muted' }}">
                                    <i class="fas fa-{{ $user->notifications_enabled ? 'check' : 'times' }}-circle {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                    {{ $user->notifications_enabled ? __('admin-dashboard.enabled') : __('admin-dashboard.disabled') }}
                                </strong>
                            </div>
                        @endif

                        <div>
                            <span>{{ $text('Default address', 'العنوان الافتراضي') }}</span>
                            <strong>{{ $defaultAddress ? $addressLabel($defaultAddress) : __('admin-dashboard.not_provided') }}</strong>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-8">
                <section class="usd-card h-100">
                    <div class="usd-section-head">
                        <div>
                            <h5>{{ __('admin-dashboard.contact_information') }}</h5>
                            <p>{{ $text('Email and phone verification status.', 'حالة توثيق البريد والهاتف.') }}</p>
                        </div>
                    </div>

                    <div class="usd-contact-grid">
                        <div class="usd-contact-card">
                            <div class="usd-contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>

                            <div class="usd-contact-content">
                                <span>{{ __('admin-dashboard.user_email') }}</span>

                                @if($user->email)
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                @else
                                    <strong>{{ __('admin-dashboard.not_provided') }}</strong>
                                @endif

                                @if($user->email_verified_at)
                                    <small class="usd-inline-status success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('admin-dashboard.verified') }}
                                    </small>
                                @else
                                    <small class="usd-inline-status danger">
                                        <i class="fas fa-times-circle"></i>
                                        {{ __('admin-dashboard.not_verified') }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <div class="usd-contact-card">
                            <div class="usd-contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>

                            <div class="usd-contact-content">
                                <span>{{ __('admin-dashboard.user_phone') }}</span>

                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                @else
                                    <strong>{{ __('admin-dashboard.not_provided') }}</strong>
                                @endif

                                <small class="usd-inline-status {{ $user->phone_verified_at ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $user->phone_verified_at ? 'check' : 'times' }}-circle"></i>
                                    {{ __('admin-dashboard.' . ($user->phone_verified_at ? 'verified' : 'not_verified')) }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="usd-activity-strip">
                        <div>
                            <span>{{ __('admin-dashboard.shipments') }}</span>
                            <strong>{{ $user->orders_count }}</strong>
                        </div>

                        <div>
                            <span>{{ __('admin-dashboard.ecommerce_orders') }}</span>
                            <strong>{{ $user->ecommerce_orders_count }}</strong>
                        </div>

                        <div>
                            <span>{{ __('admin-dashboard.addresses') }}</span>
                            <strong>{{ $addressesCount }}</strong>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="usd-card">
            <div class="usd-section-head">
                <div>
                    <h5>{{ __('admin-dashboard.addresses') }}</h5>
                    <p>
                        {{ $addressesCount
                            ? $text('Saved delivery addresses for this user.', 'العناوين المحفوظة لهذا المستخدم.')
                            : $text('No saved addresses are available yet.', 'لا توجد عناوين محفوظة حتى الآن.')
                        }}
                    </p>
                </div>

                <span class="usd-count-pill">
                    <i class="fas fa-map-marker-alt {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $addressesCount }}
                </span>
            </div>

            @if($user->addresses && $user->addresses->count())
                <div class="usd-address-grid">
                    @foreach($user->addresses as $address)
                        @php
                            $typeTone = $addressTypeTone($address->address_type);
                            $typeIcon = $addressTypeIcon($address->address_type);
                        @endphp

                        <article class="usd-address-card {{ $address->is_default ? 'is-default' : '' }}">
                            <div class="usd-address-top">
                                <div class="usd-address-badges">
                                    <span class="usd-address-type usd-address-type-{{ $typeTone }}">
                                        <i class="fas {{ $typeIcon }}"></i>
                                        {{ $addressTypeLabel($address->address_type) }}
                                    </span>

                                    @if($address->is_default)
                                        <span class="usd-address-default">
                                            <i class="fas fa-star"></i>
                                            {{ __('admin-dashboard.default') }}
                                        </span>
                                    @endif
                                </div>

                                <small>{{ $address->created_at->format('M d, Y') }}</small>
                            </div>

                            <div class="usd-address-main">
                                <h6>{{ $address->street_name ?: __('admin-dashboard.not_provided') }}</h6>
                                <p>{{ $addressLabel($address) }}</p>
                            </div>

                            <div class="usd-address-details">
                                @if($address->city)
                                    <div>
                                        <span>{{ __('admin-dashboard.city') }}</span>
                                        <strong>{{ optional($address->city)->{"name_{$locale}"} ?? $address->city->name_en }}</strong>
                                    </div>
                                @endif

                                @if($address->state)
                                    <div>
                                        <span>{{ __('admin-dashboard.state') }}</span>
                                        <strong>{{ optional($address->state)->{"name_{$locale}"} ?? $address->state->name_en }}</strong>
                                    </div>
                                @endif

                                <div>
                                    <span>{{ __('messages.building') }}</span>
                                    <strong>{{ $address->building ?: '--' }}</strong>
                                </div>

                                <div>
                                    <span>{{ __('messages.floor') }}</span>
                                    <strong>{{ $address->floor ?: '--' }}</strong>
                                </div>

                                @if($address->landmark)
                                    <div class="usd-address-wide">
                                        <span>{{ __('messages.landmark') }}</span>
                                        <strong>{{ $address->landmark }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="usd-address-footer">
                                <span>
                                    <i class="fas fa-hashtag"></i>
                                    ID: {{ $address->id }}
                                </span>

                                <span class="usd-area-badge">
                                    {{ $address->is_village ? __('admin-dashboard.village') : __('admin-dashboard.city_area') }}
                                </span>
                            </div>

                            @if($address->latitude && $address->longitude)
                                <a
                                    href="https://maps.google.com/?q={{ $address->latitude }},{{ $address->longitude }}"
                                    target="_blank"
                                    class="btn btn-sm usd-map-btn"
                                >
                                    <i class="fas fa-map-marked-alt {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                    {{ __('admin-dashboard.view_on_map') }}
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="usd-empty">
                    <div class="usd-empty-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>

                    <h5>{{ __('admin-dashboard.no_addresses_found') }}</h5>

                    <p>{{ $text('This user has not saved any addresses yet.', 'هذا المستخدم لم يقم بحفظ أي عناوين بعد.') }}</p>
                </div>
            @endif
        </section>

        <div class="usd-footer-actions">
            <a href="{{ route('admin.users') }}" class="btn usd-back-btn">
                <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
                {{ __('admin-dashboard.back_to_list') }}
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .usd-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .usd-hero-card,
        .usd-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .usd-hero-card {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .usd-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .usd-hero-avatar,
        .usd-hero-avatar-img {
            width: 70px;
            height: 70px;
            border-radius: 24px;
            flex-shrink: 0;
        }

        .usd-hero-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: 1.8rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .usd-hero-avatar-img {
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .usd-chip {
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

        .usd-hero-text h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .usd-meta-row,
        .usd-badges-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
            margin-top: .65rem;
        }

        .usd-meta-pill,
        .usd-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .38rem .7rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .usd-meta-pill {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
        }

        .usd-badge-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .usd-badge-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .usd-hero-side {
            display: grid;
            gap: .75rem;
            min-width: 240px;
            flex-shrink: 0;
        }

        .usd-score-card {
            padding: .85rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .usd-score-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .45rem;
        }

        .usd-score-top span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
        }

        .usd-score-top strong {
            color: #111827;
            font-size: .9rem;
            font-weight: 900;
        }

        .usd-score-bar {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .usd-score-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #2563eb;
        }

        .usd-back-btn {
            min-height: 42px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .usd-back-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .usd-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .usd-metric-item {
            min-height: 78px;
            padding: .9rem 1rem;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .usd-metric-item span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
            margin-bottom: .35rem;
        }

        .usd-metric-item strong {
            color: #111827;
            font-size: 1.4rem;
            font-weight: 900;
            line-height: 1;
        }

        .usd-metric-date {
            font-size: .95rem !important;
            line-height: 1.4 !important;
        }

        .usd-card {
            padding: 1.25rem;
        }

        .usd-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .usd-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .usd-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .usd-profile-box {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .9rem;
            margin-bottom: 1rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .usd-profile-box img,
        .usd-profile-box span {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            flex-shrink: 0;
        }

        .usd-profile-box img {
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }

        .usd-profile-box span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-weight: 900;
        }

        .usd-profile-box strong {
            display: block;
            color: #111827;
            font-weight: 900;
            line-height: 1.4;
        }

        .usd-profile-box small {
            display: block;
            color: #64748b;
            font-size: .8rem;
        }

        .usd-info-list {
            display: grid;
            gap: .7rem;
        }

        .usd-info-list div {
            padding: .8rem .9rem;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .usd-info-list span {
            display: block;
            margin-bottom: .25rem;
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
        }

        .usd-info-list strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 800;
            line-height: 1.6;
            word-break: break-word;
        }

        .usd-info-list small {
            display: block;
            margin-top: .15rem;
            color: #64748b;
            font-size: .76rem;
        }

        .usd-text-success {
            color: #047857 !important;
        }

        .usd-text-muted {
            color: #64748b !important;
        }

        .usd-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }

        .usd-contact-card {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            min-width: 0;
        }

        .usd-contact-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .usd-contact-content {
            min-width: 0;
        }

        .usd-contact-content span {
            display: block;
            color: #64748b;
            font-size: .78rem;
            font-weight: 900;
            margin-bottom: .25rem;
        }

        .usd-contact-content a,
        .usd-contact-content strong {
            display: block;
            color: #111827;
            font-size: .92rem;
            font-weight: 900;
            line-height: 1.5;
            word-break: break-word;
            text-decoration: none;
        }

        .usd-contact-content a:hover {
            color: #1d4ed8;
        }

        .usd-inline-status {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            margin-top: .45rem;
            padding: .22rem .55rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 900;
        }

        .usd-inline-status.success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .usd-inline-status.danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .usd-activity-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1rem;
        }

        .usd-activity-strip div {
            padding: .85rem;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .usd-activity-strip span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 900;
            margin-bottom: .25rem;
        }

        .usd-activity-strip strong {
            color: #111827;
            font-size: 1.2rem;
            font-weight: 900;
        }

        .usd-count-pill {
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

        .usd-address-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .usd-address-card {
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            background: #fcfdff;
        }

        .usd-address-card.is-default {
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .usd-address-top,
        .usd-address-footer {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
        }

        .usd-address-top small,
        .usd-address-footer span {
            color: #64748b;
            font-size: .76rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .usd-address-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .usd-address-type,
        .usd-address-default,
        .usd-area-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .6rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 900;
        }

        .usd-address-type-primary {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .usd-address-type-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .usd-address-type-secondary {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        .usd-address-default {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
        }

        .usd-address-main {
            margin-top: .9rem;
        }

        .usd-address-main h6 {
            margin: 0;
            color: #111827;
            font-size: .98rem;
            font-weight: 900;
        }

        .usd-address-main p {
            margin: .4rem 0 0;
            color: #334155;
            font-size: .85rem;
            line-height: 1.7;
        }

        .usd-address-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .65rem;
            margin-top: .9rem;
        }

        .usd-address-details div {
            padding: .7rem;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .usd-address-details span {
            display: block;
            color: #64748b;
            font-size: .72rem;
            font-weight: 900;
            margin-bottom: .2rem;
        }

        .usd-address-details strong {
            display: block;
            color: #111827;
            font-size: .84rem;
            font-weight: 800;
            line-height: 1.5;
            word-break: break-word;
        }

        .usd-address-wide {
            grid-column: 1 / -1;
        }

        .usd-address-footer {
            margin-top: .9rem;
            padding-top: .8rem;
            border-top: 1px solid #e5e7eb;
            align-items: center;
        }

        .usd-area-badge {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569 !important;
        }

        .usd-map-btn {
            margin-top: .9rem;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
            background: #fff;
            color: #1d4ed8;
            font-weight: 900;
        }

        .usd-map-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .usd-empty {
            padding: 3rem 1.5rem;
            text-align: center;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
        }

        .usd-empty-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .usd-empty h5 {
            color: #111827;
            font-weight: 900;
            margin-bottom: .4rem;
        }

        .usd-empty p {
            max-width: 520px;
            margin: 0 auto;
            color: #64748b;
            line-height: 1.7;
        }

        .usd-footer-actions {
            display: flex;
            justify-content: flex-start;
        }

        @media (max-width: 1199.98px) {
            .usd-hero-card {
                align-items: stretch;
                flex-direction: column;
            }

            .usd-hero-side {
                min-width: 0;
            }

            .usd-address-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .usd-hero-card,
            .usd-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .usd-hero-main {
                flex-direction: column;
            }

            .usd-hero-text h3 {
                font-size: 1.2rem;
            }

            .usd-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .usd-section-head,
            .usd-address-top,
            .usd-address-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .usd-contact-grid,
            .usd-activity-strip,
            .usd-address-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush