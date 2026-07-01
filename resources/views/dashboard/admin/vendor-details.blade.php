@extends('layouts.admin')

@section('title', __('admin-dashboard.vendor_details') . ' - ' . $vendor->name)
@section('page-title', __('admin-dashboard.vendor_details_title', ['name' => $vendor->name]))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $displayName = $vendor->name ?: $text('Vendor', 'بائع');
    $initial = mb_substr($displayName, 0, 1);

    $commission = $vendorCommission ?? $publicCommission;

    $readinessChecks = [
        filled($vendor->name),
        filled($vendor->email),
        filled($vendor->phone),
        filled($vendor->address),
        filled($vendor->logo),
        (bool) $vendor->email_verified,
        (bool) $vendor->phone_verified,
        (int) ($vendor->products_count ?? 0) > 0,
        (int) ($vendor->total_orders_count ?? 0) > 0,
        (bool) $commission,
        (bool) $vendor->is_active,
    ];

    $readinessScore = (int) round((collect($readinessChecks)->filter()->count() / count($readinessChecks)) * 100);

    $statusTone = $vendor->is_active ? 'success' : 'danger';
    $statusLabel = $vendor->is_active
        ? __('admin-dashboard.vendor_active')
        : __('admin-dashboard.vendor_inactive');

    $verificationScore = (int) round((collect([
        (bool) $vendor->email_verified,
        (bool) $vendor->phone_verified,
    ])->filter()->count() / 2) * 100);

    $mapUrl = $vendor->latitude && $vendor->longitude
        ? 'https://www.google.com/maps?q=' . $vendor->latitude . ',' . $vendor->longitude
        : null;
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.vendors') }}">{{ __('admin-dashboard.vendors') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ $vendor->name }}</li>
@endsection

@section('page-actions')
    <div class="vds-actions-top" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="btn vds-action-primary">
            <i class="fas fa-boxes {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
            {{ __('admin-dashboard.view_all_products', ['count' => $vendor->products_count]) }}
        </a>

        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn vds-action-light">
            <i class="fas fa-edit {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
            {{ __('admin-dashboard.edit_vendor') }}
        </a>

        <button
            type="button"
            class="btn {{ $vendor->is_active ? 'vds-action-warning' : 'vds-action-success' }} js-vendor-status-btn"
            data-action="{{ route('admin.vendors.toggle-status', $vendor->id) }}"
            data-vendor-name="{{ $displayName }}"
            data-message="{{ $vendor->is_active ? __('admin-dashboard.vendor_confirm_deactivate') : __('admin-dashboard.vendor_confirm_activate') }}"
            data-confirm-label="{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}"
            data-mode="{{ $vendor->is_active ? 'deactivate' : 'activate' }}"
        >
            <i class="fas fa-{{ $vendor->is_active ? 'pause' : 'play' }} {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
            {{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}
        </button>

        <a href="{{ route('admin.vendors.export', $vendor->id) }}" class="btn vds-action-success">
            <i class="fas fa-file-excel {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
            {{ __('admin-dashboard.export_excel') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="vds-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="vds-hero-card">
            <div class="vds-hero-main">
                @if($vendor->logo)
                    <img src="{{ asset($vendor->logo) }}" alt="{{ $displayName }}" class="vds-hero-logo">
                @else
                    <span class="vds-hero-avatar">{{ $initial }}</span>
                @endif

                <div class="vds-hero-text">
                    <span class="vds-chip">
                        {{ __('admin-dashboard.vendor_information') }}
                    </span>

                    <h3>{{ $displayName }}</h3>

                    <div class="vds-meta-row">
                        <span class="vds-meta-pill">
                            <i class="fas fa-hashtag"></i>
                            {{ $vendor->vendor_number ?? ('#' . $vendor->id) }}
                        </span>

                        <span class="vds-status vds-status-{{ $statusTone }}">
                            <i></i>
                            {{ $statusLabel }}
                        </span>

                        <span class="vds-meta-pill">
                            <i class="far fa-calendar-alt"></i>
                            {{ $vendor->created_at->format('M d, Y') }}
                        </span>
                    </div>

                    <div class="vds-contact-inline">
                        @if($vendor->email)
                            <a href="mailto:{{ $vendor->email }}">
                                <i class="far fa-envelope"></i>
                                {{ $vendor->email }}
                            </a>
                        @endif

                        @if($vendor->phone)
                            <a href="tel:{{ $vendor->phone }}">
                                <i class="fas fa-phone"></i>
                                {{ $vendor->phone }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="vds-hero-side">
                <div class="vds-score-card">
                    <div class="vds-score-top">
                        <span>{{ $text('Vendor readiness', 'جاهزية البائع') }}</span>
                        <strong>{{ $readinessScore }}%</strong>
                    </div>

                    <div class="vds-score-bar">
                        <span style="width: {{ $readinessScore }}%"></span>
                    </div>
                </div>

                <a href="{{ route('admin.vendors') }}" class="btn vds-back-btn">
                    <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
                    {{ __('admin-dashboard.back_to_list') ?? $text('Back to list', 'العودة للقائمة') }}
                </a>
            </div>
        </section>

        <section class="vds-metrics">
            <div class="vds-metric-item">
                <span>{{ __('admin-dashboard.vendor_total_products') }}</span>
                <strong>{{ $vendor->products_count }}</strong>
            </div>

            <div class="vds-metric-item">
                <span>{{ __('admin-dashboard.vendor_total_orders') }}</span>
                <strong>{{ $vendor->total_orders_count }}</strong>
            </div>

            <div class="vds-metric-item">
                <span>{{ $text('Verification', 'التوثيق') }}</span>
                <strong>{{ $verificationScore }}%</strong>
            </div>

            <div class="vds-metric-item">
                <span>{{ __('admin-dashboard.vendor_commissions') }}</span>
                <strong class="vds-metric-text">
                    {{ $vendorCommission ? __('admin-dashboard.custom_commission') : __('admin-dashboard.public_commission') }}
                </strong>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <section class="vds-card h-100">
                    <div class="vds-section-head">
                        <div>
                            <h5>{{ __('admin-dashboard.vendor_information') }}</h5>
                            <p>{{ $text('Main vendor profile and verification status.', 'البيانات الأساسية وحالة توثيق البائع.') }}</p>
                        </div>
                    </div>

                    <div class="vds-profile-box">
                        @if($vendor->logo)
                            <img src="{{ asset($vendor->logo) }}" alt="{{ $displayName }}">
                        @else
                            <span>{{ $initial }}</span>
                        @endif

                        <div>
                            <strong>{{ $displayName }}</strong>
                            <small>{{ $vendor->vendor_number ?? ('#' . $vendor->id) }}</small>
                        </div>
                    </div>

                    <div class="vds-info-list">
                        <div>
                            <span>{{ __('admin-dashboard.registration_date') }}</span>
                            <strong>{{ $vendor->created_at->format('F j, Y \a\t g:i A') }}</strong>
                            <small>{{ $vendor->created_at->diffForHumans() }}</small>
                        </div>

                        <div>
                            <span>{{ __('admin-dashboard.vendor_status') }}</span>
                            <strong class="vds-text-{{ $vendor->is_active ? 'success' : 'danger' }}">
                                <i class="fas fa-{{ $vendor->is_active ? 'check' : 'times' }}-circle {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $statusLabel }}
                            </strong>
                        </div>

                        <div>
                            <span>{{ __('admin-dashboard.verification_status') }}</span>

                            <div class="vds-mini-statuses">
                                <span class="vds-mini-badge {{ $vendor->email_verified ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $vendor->email_verified ? 'check' : 'times' }}-circle"></i>
                                    {{ $vendor->email_verified ? __('admin-dashboard.email_verified') : __('admin-dashboard.email_not_verified') }}
                                </span>

                                <span class="vds-mini-badge {{ $vendor->phone_verified ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $vendor->phone_verified ? 'check' : 'times' }}-circle"></i>
                                    {{ $vendor->phone_verified ? __('admin-dashboard.phone_verified') : __('admin-dashboard.phone_not_verified') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-8">
                <section class="vds-card h-100">
                    <div class="vds-section-head">
                        <div>
                            <h5>{{ __('admin-dashboard.contact_information') }}</h5>
                            <p>{{ $text('Vendor contact channels and location.', 'قنوات التواصل وموقع البائع.') }}</p>
                        </div>
                    </div>

                    <div class="vds-contact-grid">
                        <div class="vds-contact-card">
                            <div class="vds-contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>

                            <div>
                                <span>{{ __('admin-dashboard.vendor_email') }}</span>

                                @if($vendor->email)
                                    <a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>
                                @else
                                    <strong>{{ $text('Not provided', 'غير متوفر') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="vds-contact-card">
                            <div class="vds-contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>

                            <div>
                                <span>{{ __('admin-dashboard.vendor_phone') }}</span>

                                @if($vendor->phone)
                                    <a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>
                                @else
                                    <strong>{{ $text('Not provided', 'غير متوفر') }}</strong>
                                @endif
                            </div>
                        </div>

                        <div class="vds-contact-card vds-contact-wide">
                            <div class="vds-contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>

                            <div>
                                <span>{{ __('admin-dashboard.vendor_address') }}</span>
                                <strong>{{ $vendor->address ?: $text('Not provided', 'غير متوفر') }}</strong>

                                @if($mapUrl)
                                    <a href="{{ $mapUrl }}" target="_blank" class="btn btn-sm vds-map-btn">
                                        <i class="fas fa-map-marked-alt {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                        {{ __('admin-dashboard.view_on_map') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="vds-activity-strip">
                        <a href="{{ route('admin.vendors.products', $vendor->id) }}">
                            <span>{{ __('admin-dashboard.vendor_total_products') }}</span>
                            <strong>{{ $vendor->products_count }}</strong>
                        </a>

                        <a href="{{ route('admin.vendors.orders', $vendor->id) }}">
                            <span>{{ __('admin-dashboard.vendor_total_orders') }}</span>
                            <strong>{{ $vendor->total_orders_count }}</strong>
                        </a>

                        <div>
                            <span>{{ $text('Readiness', 'الجاهزية') }}</span>
                            <strong>{{ $readinessScore }}%</strong>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <section class="vds-card">
            <div class="vds-section-head">
                <div>
                    <h5>{{ __('admin-dashboard.recent_products') }}</h5>
                    <p>{{ $text('Latest products from this vendor.', 'أحدث منتجات هذا البائع.') }}</p>
                </div>

                <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="btn btn-sm vds-light-btn">
                    {{ __('admin-dashboard.view_all_products', ['count' => $vendor->products_count]) }}
                    <i class="fas {{ $isArabic ? 'fa-arrow-left me-1' : 'fa-arrow-right ms-1' }}"></i>
                </a>
            </div>

            @if($recentProducts->count() > 0)
                <div class="table-responsive vds-table-wrap">
                    <table class="table align-middle mb-0 vds-table">
                        <thead>
                            <tr>
                                <th>{{ __('admin-dashboard.product_name') }}</th>
                                <th>{{ __('admin-dashboard.product_price') }}</th>
                                <th>{{ __('admin-dashboard.product_stock') }}</th>
                                <th>{{ __('admin-dashboard.product_status') }}</th>
                                <th class="text-end">{{ __('admin-dashboard.product_actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($recentProducts as $product)
                                <tr>
                                    <td>
                                        <div class="vds-product-cell">
                                            @if($product->images->isNotEmpty())
                                                <img src="{{ asset($product->images->first()->url) }}" alt="{{ $product->name }}">
                                            @else
                                                <span>
                                                    <i class="fas fa-box"></i>
                                                </span>
                                            @endif

                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <small>#{{ $product->id }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vds-price">
                                            {{ number_format($product->price, 2) }}
                                            {{ __('admin-dashboard.EGP') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="vds-stock-pill">
                                            <i class="fas fa-boxes"></i>
                                            {{ $product->stock }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="vds-status vds-status-{{ $product->is_active ? 'success' : 'secondary' }}">
                                            <i></i>
                                            {{ $product->is_active ? __('admin-dashboard.vendor_active') : __('admin-dashboard.vendor_inactive') }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <a
                                            href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.show']) }}"
                                            class="btn btn-sm vds-view-btn"
                                        >
                                            <i class="fas fa-eye {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                            {{ $text('View', 'عرض') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="vds-empty">
                    <div class="vds-empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>

                    <h5>{{ __('admin-dashboard.no_products_found') }}</h5>
                    <p>{{ __('admin-dashboard.no_products_message') }}</p>
                </div>
            @endif
        </section>

        <section class="vds-card">
            <div class="vds-section-head">
                <div>
                    <h5>
                        <i class="fas fa-coins {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.vendor_commissions') }}
                    </h5>

                    <p>
                        {{ $vendorCommission
                            ? $text('This vendor uses a custom commission profile.', 'هذا البائع يستخدم إعداد عمولة مخصص.')
                            : $text('This vendor currently uses the public commission profile.', 'هذا البائع يستخدم إعداد العمولة العام حالياً.')
                        }}
                    </p>
                </div>

                <span class="vds-commission-badge {{ $vendorCommission ? 'custom' : 'public' }}">
                    {{ $vendorCommission ? __('admin-dashboard.custom_commission') : __('admin-dashboard.public_commission') }}
                </span>
            </div>

            @if($commission)
                <div class="vds-commission-grid">
                    <div>
                        <span>{{ __('admin-dashboard.annual_subscription_egp') }}</span>
                        <strong>{{ $commission->annual_subscription }} {{ __('admin-dashboard.EGP') }}</strong>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.order_commission_percent') }}</span>
                        <strong>{{ $commission->order_commission_percent }}%</strong>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.min_order_commission_egp') }}</span>
                        <strong>{{ $commission->order_commission_min }} {{ __('admin-dashboard.EGP') }}</strong>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.refund_fee_percent') }}</span>
                        <strong>{{ $commission->refund_fee_percent }}%</strong>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.min_refund_fee_egp') }}</span>
                        <strong>{{ $commission->refund_fee_min }} {{ __('admin-dashboard.EGP') }}</strong>
                    </div>

                    <div>
                        <span>{{ __('admin-dashboard.annual_target_commission') }}</span>
                        <strong>{{ $commission->annual_target_commission }} {{ __('admin-dashboard.EGP') }}</strong>
                    </div>
                </div>

                @if(!$vendorCommission)
                    <div class="vds-helper-note mt-3">
                        <i class="fas fa-info-circle {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.using_public_commission') }}
                    </div>
                @endif
            @else
                <div class="vds-empty compact">
                    <div class="vds-empty-icon">
                        <i class="fas fa-coins"></i>
                    </div>

                    <h5>{{ __('admin-dashboard.no_commission_found') }}</h5>
                </div>
            @endif

            <div class="vds-card-footer-actions">
                <button
                    type="button"
                    class="btn vds-action-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#commissionModal"
                >
                    <i class="fas fa-edit {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $vendorCommission
                        ? __('admin-dashboard.edit_commission')
                        : __('admin-dashboard.create_custom_commission') }}
                </button>
            </div>
        </section>
    </div>

    <div class="modal fade" id="commissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content vds-modal">
                <form method="POST"
                      action="{{ $vendorCommission
                            ? route('admin.vendors.commission.update', $vendor->id)
                            : route('admin.vendors.commission.store', $vendor->id) }}">
                    @csrf

                    @if($vendorCommission)
                        @method('PATCH')
                    @endif

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title">
                                <i class="fas fa-coins {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ __('admin-dashboard.vendor_commissions') }}
                            </h5>

                            <p class="vds-modal-subtitle mb-0">
                                {{ $vendorCommission
                                    ? $text('Edit this vendor custom commission values.', 'تعديل قيم العمولة المخصصة لهذا البائع.')
                                    : $text('Create a custom commission profile for this vendor.', 'إنشاء إعداد عمولة مخصص لهذا البائع.')
                                }}
                            </p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.annual_subscription_egp') }}</label>
                                <input type="number" step="0.01" name="annual_subscription"
                                       class="form-control vds-control"
                                       value="{{ old('annual_subscription', $commission->annual_subscription ?? '') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.order_commission_percent') }}</label>
                                <input type="number" step="0.01" name="order_commission_percent"
                                       class="form-control vds-control"
                                       value="{{ old('order_commission_percent', $commission->order_commission_percent ?? '') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.min_order_commission_egp') }}</label>
                                <input type="number" step="0.01" name="order_commission_min"
                                       class="form-control vds-control"
                                       value="{{ old('order_commission_min', $commission->order_commission_min ?? '') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.refund_fee_percent') }}</label>
                                <input type="number" step="0.01" name="refund_fee_percent"
                                       class="form-control vds-control"
                                       value="{{ old('refund_fee_percent', $commission->refund_fee_percent ?? '') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.min_refund_fee_egp') }}</label>
                                <input type="number" step="0.01" name="refund_fee_min"
                                       class="form-control vds-control"
                                       value="{{ old('refund_fee_min', $commission->refund_fee_min ?? '') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="vds-label">{{ __('admin-dashboard.annual_target_commission') }}</label>
                                <input type="number" step="0.01" name="annual_target_commission"
                                       class="form-control vds-control"
                                       value="{{ old('annual_target_commission', $commission->annual_target_commission ?? '') }}"
                                       required>
                            </div>
                        </div>

                        @if(!$vendorCommission)
                            <div class="vds-helper-note mt-3 warning">
                                <i class="fas fa-exclamation-triangle {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ __('admin-dashboard.this_will_create_custom_commission') }}
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn vds-back-btn" data-bs-dismiss="modal">
                            {{ __('admin-dashboard.cancel') }}
                        </button>

                        <button type="submit" class="btn vds-action-primary">
                            <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                            {{ __('admin-dashboard.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vendorStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content vds-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            {{ $text('Confirm status change', 'تأكيد تغيير الحالة') }}
                        </h5>

                        <p class="vds-modal-subtitle mb-0" id="vendorStatusModalSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" id="vendorStatusForm">
                    @csrf
                    @method('PATCH')

                    <div class="modal-body">
                        <div class="vds-modal-warning">
                            <div>
                                <i class="fas fa-store"></i>
                            </div>

                            <p id="vendorStatusModalMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn vds-back-btn" data-bs-dismiss="modal">
                            {{ __('admin-dashboard.cancel') }}
                        </button>

                        <button type="submit" class="btn vds-modal-confirm" id="vendorStatusConfirmBtn">
                            {{ $text('Confirm', 'تأكيد') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .vds-page {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .vds-actions-top {
                display: flex;
                flex-wrap: wrap;
                gap: .5rem;
                align-items: center;
            }

            .vds-action-primary,
            .vds-action-light,
            .vds-action-success,
            .vds-action-warning,
            .vds-back-btn,
            .vds-light-btn {
                min-height: 42px;
                border-radius: 999px;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .vds-action-primary {
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
                box-shadow: 0 10px 18px rgba(37, 99, 235, .14);
            }

            .vds-action-primary:hover {
                background: #1d4ed8;
                border-color: #1d4ed8;
                color: #fff;
            }

            .vds-action-light,
            .vds-back-btn,
            .vds-light-btn {
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #475569;
            }

            .vds-action-light:hover,
            .vds-back-btn:hover,
            .vds-light-btn:hover {
                background: #f8fafc;
                color: #111827;
            }

            .vds-action-success {
                background: #047857;
                border-color: #047857;
                color: #fff;
            }

            .vds-action-success:hover {
                background: #065f46;
                border-color: #065f46;
                color: #fff;
            }

            .vds-action-warning {
                background: #fffbeb;
                border: 1px solid #fde68a;
                color: #b45309;
            }

            .vds-action-warning:hover {
                background: #b45309;
                border-color: #b45309;
                color: #fff;
            }

            .vds-hero-card,
            .vds-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            }

            .vds-hero-card {
                padding: 1.25rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
            }

            .vds-hero-main {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
                min-width: 0;
            }

            .vds-hero-logo,
            .vds-hero-avatar {
                width: 72px;
                height: 72px;
                border-radius: 24px;
                flex-shrink: 0;
            }

            .vds-hero-logo {
                object-fit: cover;
                border: 1px solid #e2e8f0;
                background: #fff;
            }

            .vds-hero-avatar {
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

            .vds-chip {
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

            .vds-hero-text h3 {
                margin: 0;
                color: #111827;
                font-size: 1.45rem;
                font-weight: 900;
                letter-spacing: -.02em;
            }

            .vds-meta-row,
            .vds-contact-inline {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .5rem;
                margin-top: .65rem;
            }

            .vds-meta-pill,
            .vds-status {
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

            .vds-meta-pill {
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                color: #475569;
            }

            .vds-status {
                border: 1px solid transparent;
            }

            .vds-status i {
                width: .45rem;
                height: .45rem;
                border-radius: 999px;
                background: currentColor;
            }

            .vds-status-success {
                background: #ecfdf5;
                border-color: #a7f3d0;
                color: #047857;
            }

            .vds-status-danger {
                background: #fef2f2;
                border-color: #fecaca;
                color: #b91c1c;
            }

            .vds-status-secondary {
                background: #f8fafc;
                border-color: #cbd5e1;
                color: #475569;
            }

            .vds-contact-inline a {
                color: #334155;
                text-decoration: none;
                font-size: .82rem;
                font-weight: 800;
            }

            .vds-contact-inline a:hover {
                color: #1d4ed8;
            }

            .vds-hero-side {
                min-width: 240px;
                display: grid;
                gap: .75rem;
            }

            .vds-score-card {
                padding: .85rem;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
            }

            .vds-score-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .75rem;
                margin-bottom: .45rem;
            }

            .vds-score-top span {
                color: #64748b;
                font-size: .78rem;
                font-weight: 900;
            }

            .vds-score-top strong {
                color: #111827;
                font-size: .9rem;
                font-weight: 900;
            }

            .vds-score-bar {
                height: 8px;
                border-radius: 999px;
                background: #e5e7eb;
                overflow: hidden;
            }

            .vds-score-bar span {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: #2563eb;
            }

            .vds-metrics {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: .75rem;
            }

            .vds-metric-item {
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

            .vds-metric-item span {
                color: #64748b;
                font-size: .78rem;
                font-weight: 900;
                margin-bottom: .35rem;
            }

            .vds-metric-item strong {
                color: #111827;
                font-size: 1.4rem;
                font-weight: 900;
                line-height: 1;
            }

            .vds-metric-text {
                font-size: .95rem !important;
                line-height: 1.4 !important;
            }

            .vds-card {
                padding: 1.25rem;
            }

            .vds-section-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .vds-section-head h5 {
                margin: 0;
                color: #111827;
                font-size: 1rem;
                font-weight: 900;
            }

            .vds-section-head p {
                margin: .25rem 0 0;
                color: #64748b;
                font-size: .86rem;
                line-height: 1.6;
            }

            .vds-profile-box {
                display: flex;
                align-items: center;
                gap: .75rem;
                padding: .9rem;
                margin-bottom: 1rem;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
            }

            .vds-profile-box img,
            .vds-profile-box span {
                width: 46px;
                height: 46px;
                border-radius: 16px;
                flex-shrink: 0;
            }

            .vds-profile-box img {
                object-fit: cover;
                border: 1px solid #e2e8f0;
            }

            .vds-profile-box span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #eff6ff;
                border: 1px solid #dbeafe;
                color: #2563eb;
                font-weight: 900;
            }

            .vds-profile-box strong {
                display: block;
                color: #111827;
                font-weight: 900;
                line-height: 1.4;
            }

            .vds-profile-box small {
                display: block;
                color: #64748b;
                font-size: .8rem;
            }

            .vds-info-list {
                display: grid;
                gap: .7rem;
            }

            .vds-info-list div {
                padding: .8rem .9rem;
                border-radius: 14px;
                border: 1px solid #e5e7eb;
                background: #fff;
            }

            .vds-info-list span {
                display: block;
                margin-bottom: .25rem;
                color: #64748b;
                font-size: .78rem;
                font-weight: 900;
            }

            .vds-info-list strong {
                display: block;
                color: #111827;
                font-size: .9rem;
                font-weight: 800;
                line-height: 1.6;
                word-break: break-word;
            }

            .vds-info-list small {
                display: block;
                color: #64748b;
                font-size: .76rem;
                margin-top: .15rem;
            }

            .vds-text-success {
                color: #047857 !important;
            }

            .vds-text-danger {
                color: #b91c1c !important;
            }

            .vds-mini-statuses {
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
                margin-top: .4rem;
            }

            .vds-mini-badge {
                display: inline-flex;
                align-items: center;
                gap: .3rem;
                padding: .25rem .6rem;
                border-radius: 999px;
                font-size: .72rem;
                font-weight: 900;
            }

            .vds-mini-badge.success {
                background: #ecfdf5;
                color: #047857;
                border: 1px solid #a7f3d0;
            }

            .vds-mini-badge.danger {
                background: #fef2f2;
                color: #b91c1c;
                border: 1px solid #fecaca;
            }

            .vds-contact-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .9rem;
            }

            .vds-contact-card {
                display: flex;
                align-items: flex-start;
                gap: .8rem;
                padding: 1rem;
                border-radius: 18px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                min-width: 0;
            }

            .vds-contact-wide {
                grid-column: 1 / -1;
            }

            .vds-contact-icon {
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

            .vds-contact-card span {
                display: block;
                color: #64748b;
                font-size: .78rem;
                font-weight: 900;
                margin-bottom: .25rem;
            }

            .vds-contact-card a,
            .vds-contact-card strong {
                display: block;
                color: #111827;
                font-size: .92rem;
                font-weight: 900;
                line-height: 1.5;
                word-break: break-word;
                text-decoration: none;
            }

            .vds-contact-card a:hover {
                color: #1d4ed8;
            }

            .vds-map-btn {
                margin-top: .6rem;
                border-radius: 999px;
                border: 1px solid #bfdbfe;
                background: #fff;
                color: #1d4ed8;
                font-weight: 900;
            }

            .vds-map-btn:hover {
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
            }

            .vds-activity-strip {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: .75rem;
                margin-top: 1rem;
            }

            .vds-activity-strip a,
            .vds-activity-strip div {
                padding: .85rem;
                border-radius: 16px;
                background: #fff;
                border: 1px solid #e5e7eb;
                text-decoration: none;
            }

            .vds-activity-strip span {
                display: block;
                color: #64748b;
                font-size: .76rem;
                font-weight: 900;
                margin-bottom: .25rem;
            }

            .vds-activity-strip strong {
                color: #111827;
                font-size: 1.2rem;
                font-weight: 900;
            }

            .vds-table-wrap {
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                overflow: hidden;
            }

            .vds-table {
                min-width: 760px;
            }

            .vds-table thead th {
                padding: .85rem 1rem;
                background: #f8fafc;
                color: #475569;
                border-bottom: 1px solid #e5e7eb;
                font-size: .76rem;
                font-weight: 900;
                white-space: nowrap;
            }

            .vds-table tbody td {
                padding: 1rem;
                border-color: #eef2f7;
                vertical-align: middle;
            }

            .vds-table tbody tr:hover {
                background: #fbfdff;
            }

            .vds-product-cell {
                display: flex;
                align-items: center;
                gap: .75rem;
                min-width: 0;
            }

            .vds-product-cell img,
            .vds-product-cell span {
                width: 46px;
                height: 46px;
                border-radius: 16px;
                flex-shrink: 0;
            }

            .vds-product-cell img {
                object-fit: cover;
                border: 1px solid #e2e8f0;
            }

            .vds-product-cell span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #64748b;
            }

            .vds-product-cell strong {
                display: block;
                color: #111827;
                font-size: .92rem;
                font-weight: 900;
                line-height: 1.4;
            }

            .vds-product-cell small {
                display: block;
                color: #64748b;
                font-size: .76rem;
            }

            .vds-price {
                color: #111827;
                font-size: .9rem;
                font-weight: 900;
            }

            .vds-stock-pill {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                padding: .35rem .65rem;
                border-radius: 999px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1d4ed8;
                font-size: .75rem;
                font-weight: 900;
            }

            .vds-view-btn {
                border-radius: 999px;
                border: 1px solid #bfdbfe;
                background: #eff6ff;
                color: #1d4ed8;
                font-weight: 900;
            }

            .vds-view-btn:hover {
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
            }

            .vds-commission-badge {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: .38rem .75rem;
                border-radius: 999px;
                font-size: .78rem;
                font-weight: 900;
                white-space: nowrap;
            }

            .vds-commission-badge.custom {
                background: #ecfdf5;
                border: 1px solid #a7f3d0;
                color: #047857;
            }

            .vds-commission-badge.public {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                color: #475569;
            }

            .vds-commission-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: .75rem;
            }

            .vds-commission-grid div {
                padding: .9rem;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
            }

            .vds-commission-grid span {
                display: block;
                color: #64748b;
                font-size: .76rem;
                font-weight: 900;
                margin-bottom: .25rem;
            }

            .vds-commission-grid strong {
                display: block;
                color: #111827;
                font-size: .95rem;
                font-weight: 900;
                line-height: 1.5;
            }

            .vds-helper-note {
                padding: .85rem;
                border-radius: 14px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1d4ed8;
                font-size: .84rem;
                font-weight: 800;
                line-height: 1.7;
            }

            .vds-helper-note.warning {
                background: #fffbeb;
                border-color: #fde68a;
                color: #b45309;
            }

            .vds-card-footer-actions {
                display: flex;
                justify-content: flex-end;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #e5e7eb;
            }

            .vds-empty {
                padding: 3rem 1.5rem;
                text-align: center;
                border-radius: 18px;
                background: #f8fafc;
                border: 1px dashed #cbd5e1;
            }

            .vds-empty.compact {
                padding: 2rem 1rem;
            }

            .vds-empty-icon {
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

            .vds-empty h5 {
                color: #111827;
                font-weight: 900;
                margin-bottom: .4rem;
            }

            .vds-empty p {
                max-width: 520px;
                margin: 0 auto;
                color: #64748b;
                line-height: 1.7;
            }

            .vds-label {
                display: block;
                margin-bottom: .4rem;
                color: #475569;
                font-size: .8rem;
                font-weight: 900;
            }

            .vds-control {
                min-height: 44px;
                border-radius: 13px;
                border-color: #dbe3ea;
                color: #111827;
                font-size: .9rem;
                box-shadow: none;
            }

            .vds-control:focus {
                border-color: #60a5fa;
                box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
            }

            .vds-modal {
                border: 0;
                border-radius: 22px;
                overflow: hidden;
                box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
            }

            .vds-modal .modal-header,
            .vds-modal .modal-footer {
                border-color: #e5e7eb;
            }

            .vds-modal .modal-title {
                color: #111827;
                font-weight: 900;
            }

            .vds-modal-subtitle {
                color: #64748b;
                font-size: .82rem;
                margin-top: .2rem;
            }

            .vds-modal-warning {
                display: flex;
                align-items: flex-start;
                gap: .85rem;
                padding: 1rem;
                border-radius: 18px;
                background: #f8fafc;
                border: 1px dashed #cbd5e1;
            }

            .vds-modal-warning div {
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

            .vds-modal-warning p {
                margin: 0;
                color: #334155;
                line-height: 1.7;
                font-weight: 700;
            }

            .vds-modal-confirm {
                border-radius: 999px;
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
                font-weight: 900;
            }

            .vds-modal-confirm.is-danger {
                background: #b45309;
                border-color: #b45309;
            }

            .vds-modal-confirm.is-success {
                background: #047857;
                border-color: #047857;
            }

            @media (max-width: 1199.98px) {
                .vds-hero-card {
                    align-items: stretch;
                    flex-direction: column;
                }

                .vds-hero-side {
                    min-width: 0;
                }

                .vds-commission-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 767.98px) {
                .vds-hero-card,
                .vds-card {
                    padding: 1rem;
                    border-radius: 18px;
                }

                .vds-hero-main {
                    flex-direction: column;
                }

                .vds-hero-text h3 {
                    font-size: 1.2rem;
                }

                .vds-metrics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .vds-section-head,
                .vds-card-footer-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .vds-contact-grid,
                .vds-activity-strip,
                .vds-commission-grid {
                    grid-template-columns: 1fr;
                }

                .vds-table {
                    min-width: 720px;
                }
            }
        </style>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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