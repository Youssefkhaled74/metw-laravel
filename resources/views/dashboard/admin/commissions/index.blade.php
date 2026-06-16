@extends('layouts.admin')

@section('title', __('admin-dashboard.page-title'))
@section('page-title', __('admin-dashboard.commissions-fees'))

@section('content')
<div class="container-fluid px-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    {{-- Header Section with Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white shadow-lg">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h3 class="fw-bold mb-2">{{ __('admin-dashboard.commissions-fees') }}</h3>
                            <p class="mb-0 opacity-75">{{ __('admin-dashboard.commissions-description') }}</p>
                        </div>
                        <div class="col-lg-4 text-lg-{{ app()->getLocale() === 'ar' ? 'start' : 'end' }} mt-3 mt-lg-0">
                            <div class="d-inline-flex align-items-center bg-white bg-opacity-25 rounded-pill px-4 py-2">
                                <i class="fas fa-chart-line {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                <span class="small">{{ __('admin-dashboard.last-updated') }}: {{ now()->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Commission Cards Row --}}
    <div class="row g-4">

        {{-- Vendor Commission Card --}}
        <div class="col-xl-6">
            <div class="card h-100 border-0 shadow-lg hover-lift">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary-soft text-primary {{ app()->getLocale() === 'ar' ? 'ms-3' : 'me-3' }}">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.vendor-commissions') }}</h5>
                            <p class="text-muted small mb-0">{{ __('admin-dashboard.vendor-commissions-subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.commissions.vendor.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="card-body px-4">
                        {{-- Quick Stats --}}
                        <div class="row g-3 mb-4">
                            {{-- <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <span class="text-muted small d-block">{{ __('admin-dashboard.total-vendors') }}</span>
                                    <span class="h4 fw-bold mb-0">1,247</span>
                                    <span class="text-success small {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}">+12%</span>
                                </div>
                            </div> --}}
                            {{-- <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <span class="text-muted small d-block">{{ __('admin-dashboard.avg-commission') }}</span>
                                    <span class="h4 fw-bold mb-0">{{ app()->getLocale() === 'ar' ? '١٬٢٤٧' : '1,247' }} {{ __('admin-dashboard.currency-symbol') }}</span>
                                    <span class="text-info small {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}">{{ __('admin-dashboard.per-order') }}</span>
                                </div>
                            </div> --}}
                        </div>

                        {{-- Form Fields --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-calendar-alt text-primary {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.annual-subscription') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="annual_subscription"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $vendorCommission->annual_subscription ?? 1000 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.annual-subscription-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-percentage text-primary {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.order-commission-percent') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="order_commission_percent"
                                           class="form-control form-control-lg"
                                           value="{{ $vendorCommission->order_commission_percent ?? 5 }}"
                                           placeholder="{{ __('admin-dashboard.enter-percentage') }}">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.order-commission-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-coins text-primary {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.min-order-commission') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="order_commission_min"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $vendorCommission->order_commission_min ?? 5 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.min-order-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-bullseye text-primary {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.annual-target-commission') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="annual_target_commission"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $vendorCommission->annual_target_commission ?? 1000 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.annual-target-help') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <button type="submit" class="btn btn-primary btn-lg w-100 hover-lift">
                            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                            {{ __('admin-dashboard.save-vendor-settings') }}
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Shipment Commission Card --}}
        <div class="col-xl-6">
            <div class="card h-100 border-0 shadow-lg hover-lift">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success-soft text-success {{ app()->getLocale() === 'ar' ? 'ms-3' : 'me-3' }}">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.shipment-commissions') }}</h5>
                            <p class="text-muted small mb-0">{{ __('admin-dashboard.shipment-commissions-subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.commissions.shipment.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="card-body px-4">
                        {{-- Quick Stats --}}
                        <div class="row g-3 mb-4">
                            {{-- <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <span class="text-muted small d-block">{{ __('admin-dashboard.active-shipments') }}</span>
                                    <span class="h4 fw-bold mb-0">3,892</span>
                                    <span class="text-success small {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}">+8%</span>
                                </div>
                            </div> --}}
                            {{-- <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <span class="text-muted small d-block">{{ __('admin-dashboard.avg-delivery-time') }}</span>
                                    <span class="h4 fw-bold mb-0">2.4</span>
                                    <span class="text-info small {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}">{{ __('admin-dashboard.days') }}</span>
                                </div>
                            </div> --}}
                        </div>

                        {{-- Form Fields --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-calendar-alt text-success {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.annual-subscription') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="annual_subscription"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $shipmentCommission->annual_subscription ?? 100 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.shipment-annual-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-percentage text-success {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.shipment-commission-percent') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="shipment_commission_percent"
                                           class="form-control form-control-lg"
                                           value="{{ $shipmentCommission->shipment_commission_percent ?? 5 }}"
                                           placeholder="{{ __('admin-dashboard.enter-percentage') }}">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.shipment-percent-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-coins text-success {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.min-shipment-commission') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="shipment_commission_min"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $shipmentCommission->shipment_commission_min ?? 3 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.min-shipment-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-bullseye text-success {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.annual-target') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="annual_target"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $shipmentCommission->annual_target ?? 100 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.annual-shipment-help') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <button type="submit" class="btn btn-success btn-lg w-100 hover-lift">
                            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                            {{ __('admin-dashboard.save-shipment-settings') }}
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Administrative Fees Card --}}
        <div class="col-xl-6">
            <div class="card h-100 border-0 shadow-lg hover-lift">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning-soft text-warning {{ app()->getLocale() === 'ar' ? 'ms-3' : 'me-3' }}">
                            <i class="fas fa-gavel fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.administrative-fees') }}</h5>
                            <p class="text-muted small mb-0">{{ __('admin-dashboard.administrative-fees-subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.commissions.administrative.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="card-body px-4">
                        {{-- Form Fields --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-undo-alt text-warning {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.refund-fee-percent') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="refund_fee_percent"
                                           class="form-control form-control-lg"
                                           value="{{ $vendorCommission->refund_fee_percent ?? 0.50 }}"
                                           placeholder="{{ __('admin-dashboard.enter-percentage') }}">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.refund-percent-help') }}</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">
                                    <i class="fas fa-hand-holding-usd text-warning {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }} small"></i>
                                    {{ __('admin-dashboard.min-refund-fee') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light {{ app()->getLocale() === 'ar' ? 'border-start-0' : 'border-end-0' }}">
                                        {{ __('admin-dashboard.currency-symbol') }}
                                    </span>
                                    <input type="number" step="0.01" name="refund_fee_min"
                                           class="form-control form-control-lg {{ app()->getLocale() === 'ar' ? 'border-end-0 pe-0' : 'border-start-0 ps-0' }}"
                                           value="{{ $vendorCommission->refund_fee_min ?? 5 }}"
                                           placeholder="{{ __('admin-dashboard.enter-amount') }}">
                                </div>
                                <small class="text-muted">{{ __('admin-dashboard.min-refund-help') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                        <button type="submit" class="btn btn-warning btn-lg w-100 hover-lift">
                            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                            {{ __('admin-dashboard.save-administrative-fees') }}
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'me-2' : 'ms-2' }}"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-soft {
    background-color: rgba(102, 126, 234, 0.1);
}

.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.1);
}

.text-primary-soft {
    color: #667eea;
}

.text-success-soft {
    color: #28a745;
}

.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}

.form-control-lg {
    border-radius: 10px;
    min-height: 46px;
}

.input-group-text {
    border-radius: 10px;
    min-height: 46px;
}

.form-label {
    min-height: 2.4rem;
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.form-label i {
    vertical-align: middle;
}

.row.g-4 .col-md-6 {
    display: flex;
    flex-direction: column;
}

.col-md-6 .input-group {
    margin-top: auto;
}

.btn-lg {
    border-radius: 12px;
    padding: 12px 24px;
}

/* RTL Specific Adjustments */
[dir="rtl"] .input-group:not(.has-validation) > :not(:last-child):not(.dropdown-toggle):not(.dropdown-menu) {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

[dir="rtl"] .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease-out;
}
</style>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// RTL Support for Bootstrap
if (document.documentElement.dir === 'rtl') {
    // Add RTL specific Bootstrap classes
    document.body.classList.add('rtl');
}
</script>
@endsection
