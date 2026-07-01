@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة نوع نقل' : 'Add Transport Type')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة نوع نقل' : 'Add Transport Type')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $oldUnlimited = old('unlimited_capacity') ? true : false;
    $oldActive = old('is_active', true) ? true : false;
@endphp

@section('content')
    <div class="ttf-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="ttf-hero-card">
            <div class="ttf-hero-main">
                <div class="ttf-hero-icon">
                    <i class="fas fa-truck-fast"></i>
                </div>

                <div class="ttf-hero-text">
                    <span class="ttf-chip">
                        {{ $text('Transport settings', 'إعدادات النقل') }}
                    </span>

                    <h3>{{ $text('Add transport type', 'إضافة نوع نقل') }}</h3>

                    <p>
                        {{ $text(
                            'Create a new transport option with capacity rules and availability status.',
                            'أضف نوع نقل جديد مع قواعد السعة وحالة التفعيل.'
                        ) }}
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.settings.transport-types.index') }}" class="btn ttf-back-btn">
                <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
                {{ __('admin-dashboard.back_to_list') }}
            </a>
        </section>

        <form action="{{ route('admin.settings.transport-types.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <section class="ttf-card">
                        <div class="ttf-section-head">
                            <div>
                                <h5>{{ $text('Basic information', 'البيانات الأساسية') }}</h5>
                                <p>{{ $text('Code and localized names for this transport type.', 'الكود والأسماء الخاصة بنوع النقل.') }}</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="ttf-label">
                                    {{ $text('Code', 'الكود') }}
                                    <span>*</span>
                                </label>

                                <input
                                    type="text"
                                    name="code"
                                    value="{{ old('code') }}"
                                    class="form-control ttf-control @error('code') is-invalid @enderror"
                                    placeholder="{{ $text('e.g. VAN', 'مثال: VAN') }}"
                                    required
                                    data-preview="code"
                                >

                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="ttf-label">
                                    {{ $text('English name', 'الاسم بالإنجليزية') }}
                                    <span>*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name_en"
                                    value="{{ old('name_en') }}"
                                    class="form-control ttf-control @error('name_en') is-invalid @enderror"
                                    placeholder="{{ $text('e.g. Small Van', 'مثال: Small Van') }}"
                                    required
                                    data-preview="name_en"
                                >

                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="ttf-label">
                                    {{ $text('Arabic name', 'الاسم بالعربية') }}
                                </label>

                                <input
                                    type="text"
                                    name="name_ar"
                                    value="{{ old('name_ar') }}"
                                    class="form-control ttf-control @error('name_ar') is-invalid @enderror"
                                    placeholder="{{ $text('e.g. سيارة نقل صغيرة', 'مثال: سيارة نقل صغيرة') }}"
                                    data-preview="name_ar"
                                >

                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="ttf-label">
                                    {{ $text('Description', 'الوصف') }}
                                </label>

                                <textarea
                                    name="description"
                                    rows="3"
                                    class="form-control ttf-control @error('description') is-invalid @enderror"
                                    placeholder="{{ $text('Short description about when this transport type is used.', 'وصف مختصر لاستخدام نوع النقل.') }}"
                                    data-preview="description"
                                >{{ old('description') }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ttf-card">
                        <div class="ttf-section-head">
                            <div>
                                <h5>{{ $text('Capacity rules', 'قواعد السعة') }}</h5>
                                <p>{{ $text('Set weight and volume limits or mark this type as unlimited.', 'حدد حدود الوزن والحجم أو اجعل السعة غير محدودة.') }}</p>
                            </div>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="ttf-label">
                                    {{ $text('Max weight (kg)', 'الحد الأقصى للوزن') }}
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="max_weight"
                                    value="{{ old('max_weight') }}"
                                    class="form-control ttf-control @error('max_weight') is-invalid @enderror"
                                    data-capacity-input
                                    data-preview="max_weight"
                                >

                                @error('max_weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="ttf-label">
                                    {{ $text('Max volume (m³)', 'الحد الأقصى للحجم') }}
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="max_volume"
                                    value="{{ old('max_volume') }}"
                                    class="form-control ttf-control @error('max_volume') is-invalid @enderror"
                                    data-capacity-input
                                    data-preview="max_volume"
                                >

                                @error('max_volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="ttf-switch-box">
                                    <div>
                                        <strong>{{ $text('Unlimited capacity', 'سعة غير محدودة') }}</strong>
                                        <small>{{ $text('Ignore weight and volume limits.', 'تجاهل حدود الوزن والحجم.') }}</small>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="unlimited_capacity"
                                            name="unlimited_capacity"
                                            value="1"
                                            {{ $oldUnlimited ? 'checked' : '' }}
                                            data-preview="unlimited"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ttf-card">
                        <div class="ttf-section-head">
                            <div>
                                <h5>{{ $text('Availability', 'الإتاحة') }}</h5>
                                <p>{{ $text('Control whether this transport type is available for use.', 'تحكم في إتاحة نوع النقل للاستخدام.') }}</p>
                            </div>
                        </div>

                        <div class="ttf-switch-box">
                            <div>
                                <strong>{{ __('admin-dashboard.active') }}</strong>
                                <small>{{ $text('Active transport types can be used in shipment flows.', 'أنواع النقل المفعلة يمكن استخدامها في طلبات الشحن.') }}</small>
                            </div>

                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ $oldActive ? 'checked' : '' }}
                                    data-preview="active"
                                >
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-12 col-xl-4">
                    <section class="ttf-card ttf-sticky">
                        <div class="ttf-section-head">
                            <div>
                                <h5>{{ $text('Live preview', 'معاينة مباشرة') }}</h5>
                                <p>{{ $text('Quick check before saving.', 'مراجعة سريعة قبل الحفظ.') }}</p>
                            </div>
                        </div>

                        <div class="ttf-preview">
                            <div class="ttf-preview-icon">
                                <i class="fas fa-truck-fast"></i>
                            </div>

                            <span class="ttf-preview-code" id="previewCode">
                                {{ old('code') ?: $text('CODE', 'الكود') }}
                            </span>

                            <h4 id="previewName">
                                {{ old('name_en') ?: $text('Transport name', 'اسم نوع النقل') }}
                            </h4>

                            <small id="previewArabicName">
                                {{ old('name_ar') ?: $text('Arabic name will appear here', 'سيظهر الاسم العربي هنا') }}
                            </small>

                            <p id="previewDescription">
                                {{ old('description') ?: $text('Description preview will appear here.', 'ستظهر معاينة الوصف هنا.') }}
                            </p>

                            <div class="ttf-preview-meta">
                                <span id="previewCapacity">
                                    {{ $oldUnlimited ? $text('Unlimited capacity', 'سعة غير محدودة') : $text('Capacity limits', 'حدود السعة') }}
                                </span>

                                <span id="previewStatus" class="{{ $oldActive ? 'is-active' : 'is-inactive' }}">
                                    {{ $oldActive ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                </span>
                            </div>
                        </div>

                        <div class="ttf-actions">
                            <a href="{{ route('admin.settings.transport-types.index') }}" class="btn ttf-back-btn">
                                {{ __('admin-dashboard.back_to_list') }}
                            </a>

                            <button type="submit" class="btn ttf-save-btn">
                                <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Save', 'حفظ') }}
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>

    @include('dashboard.admin.settings.transport-types.partials.form-styles')
@endsection