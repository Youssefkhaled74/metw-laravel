@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مخزن جديد' : 'Add New Warehouse')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة مخزن جديد' : 'Add New Warehouse')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $oldMain = old('is_main') ? true : false;

    $completionChecks = [
        filled(old('name')),
        filled(old('phone')),
        filled(old('country_id')),
        filled(old('governorate_id')),
        filled(old('city_id')),
        filled(old('street_name')),
        filled(old('building')),
        filled(old('floor')),
        filled(old('landmark')),
    ];

    $completionScore = (int) round((collect($completionChecks)->filter()->count() / count($completionChecks)) * 100);

    $jsLabels = [
        'selectCountry' => $text('-- Select Country --', '-- اختر الدولة --'),
        'selectGovernorate' => $text('-- Select Governorate --', '-- اختر المحافظة --'),
        'selectCity' => $text('-- Select City --', '-- اختر المدينة --'),
        'loading' => $text('Loading...', 'جاري التحميل...'),
        'warehouseName' => $text('Warehouse name', 'اسم المخزن'),
        'phonePlaceholder' => $text('Phone will appear here', 'سيظهر رقم الهاتف هنا'),
        'locationPlaceholder' => $text('Select country, governorate, and city', 'اختر الدولة والمحافظة والمدينة'),
        'addressPlaceholder' => $text('Address details will appear here', 'ستظهر تفاصيل العنوان هنا'),
        'mainWarehouse' => $text('Main warehouse', 'مخزن رئيسي'),
        'regularWarehouse' => $text('Regular warehouse', 'مخزن عادي'),
        'building' => $text('Building', 'مبنى'),
        'floor' => $text('Floor', 'دور'),
    ];
@endphp

@section('page-actions')
    <a href="{{ route('admin.settings.warehouses.index') }}" class="btn whs-light-btn">
        <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
        {{ $text('Back to list', 'الرجوع للقائمة') }}
    </a>
@endsection

@section('content')
    <div class="whs-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <form action="{{ route('admin.settings.warehouses.store') }}" method="POST" id="warehouseCreateForm">
            @csrf

            <section class="whs-hero">
                <div class="whs-hero-main">
                    <div class="whs-hero-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>

                    <div class="whs-hero-copy">
                        <span class="whs-chip">
                            {{ $text('Warehouse setup', 'إعداد المخزن') }}
                        </span>

                        <h3>{{ $text('Create a new warehouse', 'إنشاء مخزن جديد') }}</h3>

                        <p>
                            {{ $text(
                                'Add warehouse contact details, location hierarchy, address information, and choose if it should be the main warehouse.',
                                'أضف بيانات المخزن والموقع والعنوان وحدد هل سيكون المخزن الرئيسي أم لا.'
                            ) }}
                        </p>
                    </div>
                </div>

                <div class="whs-hero-actions">
                    <a href="{{ route('admin.settings.warehouses.index') }}" class="btn whs-light-btn">
                        {{ $text('Cancel', 'إلغاء') }}
                    </a>

                    <button type="submit" class="btn whs-primary-btn">
                        <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Save Warehouse', 'حفظ المخزن') }}
                    </button>
                </div>
            </section>

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <section class="whs-card">
                        <div class="whs-section-head">
                            <div>
                                <h5>{{ $text('Basic information', 'البيانات الأساسية') }}</h5>
                                <p>
                                    {{ $text(
                                        'Warehouse name and phone number used by the operations team.',
                                        'اسم المخزن ورقم الهاتف المستخدمين من فريق التشغيل.'
                                    ) }}
                                </p>
                            </div>

                            <span class="whs-required-pill">
                                {{ $text('Required', 'مطلوب') }}
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="whs-label">
                                    {{ $text('Warehouse Name', 'اسم المخزن') }}
                                    <span>*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control whs-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    placeholder="{{ $text('e.g. Cairo Main Warehouse', 'مثال: مخزن القاهرة الرئيسي') }}"
                                    required
                                    data-preview="name"
                                >

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">
                                    {{ $text('Phone', 'رقم الهاتف') }}
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control whs-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}"
                                    placeholder="+20..."
                                    data-preview="phone"
                                >

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="whs-card">
                        <div class="whs-section-head">
                            <div>
                                <h5>{{ $text('Location hierarchy', 'تسلسل الموقع') }}</h5>
                                <p>
                                    {{ $text(
                                        'Select country, governorate, and city to keep delivery routing accurate.',
                                        'اختر الدولة والمحافظة والمدينة للحفاظ على دقة التوجيه والتوصيل.'
                                    ) }}
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="whs-label">
                                    {{ $text('Country', 'الدولة') }}
                                    <span>*</span>
                                </label>

                                <select
                                    name="country_id"
                                    id="country"
                                    class="form-select whs-control @error('country_id') is-invalid @enderror"
                                    required
                                    data-preview="country"
                                >
                                    <option value="">{{ $text('-- Select Country --', '-- اختر الدولة --') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                            {{ $country->{'name_' . app()->getLocale()} }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('country_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">
                                    {{ $text('Governorate', 'المحافظة') }}
                                    <span>*</span>
                                </label>

                                <select
                                    name="governorate_id"
                                    id="governorate"
                                    class="form-select whs-control @error('governorate_id') is-invalid @enderror"
                                    required
                                    disabled
                                    data-preview="governorate"
                                >
                                    <option value="">{{ $text('-- Select Governorate --', '-- اختر المحافظة --') }}</option>
                                </select>

                                @error('governorate_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">
                                    {{ $text('City', 'المدينة') }}
                                    <span>*</span>
                                </label>

                                <select
                                    name="city_id"
                                    id="city"
                                    class="form-select whs-control @error('city_id') is-invalid @enderror"
                                    required
                                    disabled
                                    data-preview="city"
                                >
                                    <option value="">{{ $text('-- Select City --', '-- اختر المدينة --') }}</option>
                                </select>

                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="whs-card">
                        <div class="whs-section-head">
                            <div>
                                <h5>{{ $text('Address details', 'تفاصيل العنوان') }}</h5>
                                <p>
                                    {{ $text(
                                        'Add street, building, floor, and landmark for easier warehouse identification.',
                                        'أضف الشارع والمبنى والدور والعلامة المميزة لتسهيل معرفة موقع المخزن.'
                                    ) }}
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="whs-label">{{ $text('Street Name', 'اسم الشارع') }}</label>

                                <input
                                    type="text"
                                    name="street_name"
                                    class="form-control whs-control @error('street_name') is-invalid @enderror"
                                    value="{{ old('street_name') }}"
                                    data-preview="street_name"
                                >

                                @error('street_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">{{ $text('Building', 'المبنى') }}</label>

                                <input
                                    type="text"
                                    name="building"
                                    class="form-control whs-control @error('building') is-invalid @enderror"
                                    value="{{ old('building') }}"
                                    data-preview="building"
                                >

                                @error('building')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">{{ $text('Floor', 'الدور') }}</label>

                                <input
                                    type="text"
                                    name="floor"
                                    class="form-control whs-control @error('floor') is-invalid @enderror"
                                    value="{{ old('floor') }}"
                                    data-preview="floor"
                                >

                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="whs-label">{{ $text('Landmark', 'علامة مميزة') }}</label>

                                <input
                                    type="text"
                                    name="landmark"
                                    class="form-control whs-control @error('landmark') is-invalid @enderror"
                                    value="{{ old('landmark') }}"
                                    data-preview="landmark"
                                >

                                @error('landmark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="whs-card">
                        <div class="whs-switch-box">
                            <div>
                                <strong>{{ $text('Main Warehouse', 'المخزن الرئيسي') }}</strong>
                                <small>
                                    {{ $text(
                                        'Enable this if the warehouse should be the operational default.',
                                        'فعّل هذا الخيار لو المخزن سيكون الافتراضي في التشغيل.'
                                    ) }}
                                </small>
                            </div>

                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    name="is_main"
                                    id="is_main"
                                    class="form-check-input"
                                    value="1"
                                    {{ old('is_main') ? 'checked' : '' }}
                                    data-preview="is_main"
                                >
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-12 col-xl-4">
                    <aside class="whs-card whs-sticky">
                        <div class="whs-section-head">
                            <div>
                                <h5>{{ $text('Review before saving', 'مراجعة قبل الحفظ') }}</h5>
                                <p>{{ $text('Live preview of the warehouse record.', 'معاينة مباشرة لبيانات المخزن.') }}</p>
                            </div>
                        </div>

                        <div class="whs-score-box">
                            <div class="whs-score-top">
                                <span>{{ $text('Setup completeness', 'اكتمال الإعداد') }}</span>
                                <strong id="previewScore">{{ $completionScore }}%</strong>
                            </div>

                            <div class="whs-score-bar">
                                <span id="previewScoreBar" style="width: {{ $completionScore }}%"></span>
                            </div>
                        </div>

                        <div class="whs-preview">
                            <div class="whs-preview-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>

                            <span class="whs-preview-chip {{ $oldMain ? 'is-main' : '' }}" id="previewMain">
                                {{ $oldMain ? $text('Main warehouse', 'مخزن رئيسي') : $text('Regular warehouse', 'مخزن عادي') }}
                            </span>

                            <h4 id="previewName">
                                {{ old('name') ?: $text('Warehouse name', 'اسم المخزن') }}
                            </h4>

                            <p id="previewPhone">
                                {{ old('phone') ?: $text('Phone will appear here', 'سيظهر رقم الهاتف هنا') }}
                            </p>

                            <div class="whs-preview-list">
                                <div>
                                    <span>{{ $text('Location', 'الموقع') }}</span>
                                    <strong id="previewLocation">
                                        {{ $text('Select country, governorate, and city', 'اختر الدولة والمحافظة والمدينة') }}
                                    </strong>
                                </div>

                                <div>
                                    <span>{{ $text('Address', 'العنوان') }}</span>
                                    <strong id="previewAddress">
                                        {{ $text('Address details will appear here', 'ستظهر تفاصيل العنوان هنا') }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="whs-guide">
                            <div class="whs-guide-item">
                                <i class="fas fa-location-dot"></i>
                                <div>
                                    <strong>{{ $text('1. Select location', '١. اختر الموقع') }}</strong>
                                    <small>{{ $text('Country, governorate, and city are required for routing.', 'الدولة والمحافظة والمدينة مطلوبة للتوجيه.') }}</small>
                                </div>
                            </div>

                            <div class="whs-guide-item">
                                <i class="fas fa-road"></i>
                                <div>
                                    <strong>{{ $text('2. Add address details', '٢. أضف تفاصيل العنوان') }}</strong>
                                    <small>{{ $text('Street and landmark help teams find the warehouse faster.', 'الشارع والعلامة المميزة يساعدوا الفريق في الوصول أسرع.') }}</small>
                                </div>
                            </div>

                            <div class="whs-guide-item">
                                <i class="fas fa-star"></i>
                                <div>
                                    <strong>{{ $text('3. Main warehouse flag', '٣. تحديد المخزن الرئيسي') }}</strong>
                                    <small>{{ $text('Use this only for the operational default warehouse.', 'استخدمها فقط للمخزن الافتراضي في التشغيل.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="whs-side-actions">
                            <button type="submit" class="btn whs-primary-btn w-100">
                                <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Save Warehouse', 'حفظ المخزن') }}
                            </button>

                            <a href="{{ route('admin.settings.warehouses.index') }}" class="btn whs-light-btn w-100">
                                {{ $text('Cancel', 'إلغاء') }}
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </form>
    </div>

    <style data-page-style>
        .whs-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .whs-hero,
        .whs-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .whs-hero {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .whs-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .whs-hero-icon {
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

        .whs-chip {
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

        .whs-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .whs-hero-copy p {
            max-width: 780px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .whs-hero-actions {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .whs-card {
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .whs-sticky {
            position: sticky;
            top: 1rem;
        }

        .whs-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .whs-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .whs-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .whs-required-pill {
            display: inline-flex;
            padding: .32rem .7rem;
            border-radius: 999px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            font-size: .73rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .whs-label {
            display: block;
            margin-bottom: .42rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 950;
        }

        .whs-label span {
            color: #dc2626;
        }

        .whs-control {
            min-height: 44px;
            border-radius: 14px !important;
            border-color: #dbe3ea !important;
            color: #111827 !important;
            background: #fff !important;
            font-size: .9rem;
            font-weight: 700;
            box-shadow: none !important;
        }

        .whs-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1) !important;
        }

        .whs-control:disabled {
            background: #f8fafc !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
        }

        .whs-switch-box {
            min-height: 80px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .whs-switch-box strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .whs-switch-box small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.6;
        }

        .whs-switch-box .form-check-input {
            width: 2.85rem;
            height: 1.45rem;
            cursor: pointer;
        }

        .whs-score-box {
            padding: .9rem;
            margin-bottom: 1rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .whs-score-top {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .5rem;
        }

        .whs-score-top span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 950;
        }

        .whs-score-top strong {
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
        }

        .whs-score-bar {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .whs-score-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #2563eb;
            transition: width .2s ease;
        }

        .whs-preview {
            padding: 1rem;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .whs-preview-icon {
            width: 62px;
            height: 62px;
            margin: 0 auto .75rem;
            border-radius: 22px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
        }

        .whs-preview-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .7rem;
            margin-bottom: .65rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .76rem;
            font-weight: 950;
        }

        .whs-preview-chip.is-main {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .whs-preview h4 {
            margin: 0;
            color: #111827;
            font-size: 1.08rem;
            font-weight: 950;
        }

        .whs-preview p {
            margin: .35rem 0 .9rem;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .whs-preview-list {
            display: grid;
            gap: .55rem;
            text-align: start;
        }

        .whs-preview-list div {
            padding: .75rem;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .whs-preview-list span {
            display: block;
            color: #64748b;
            font-size: .73rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .whs-preview-list strong {
            display: block;
            color: #111827;
            font-size: .82rem;
            font-weight: 850;
            line-height: 1.6;
        }

        .whs-guide {
            display: grid;
            gap: .7rem;
            margin-top: 1rem;
        }

        .whs-guide-item {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            padding: .85rem;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
        }

        .whs-guide-item i {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: #fff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .whs-guide-item strong {
            display: block;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 950;
            margin-bottom: .15rem;
        }

        .whs-guide-item small {
            display: block;
            color: #475569;
            font-size: .76rem;
            line-height: 1.6;
        }

        .whs-side-actions {
            display: grid;
            gap: .65rem;
            margin-top: 1rem;
        }

        .whs-light-btn,
        .whs-primary-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .whs-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .whs-light-btn:hover {
            background: #f8fafc !important;
            color: #111827 !important;
        }

        .whs-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .whs-primary-btn:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        @media (max-width: 1199.98px) {
            .whs-sticky {
                position: static;
            }

            .whs-hero {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 767.98px) {
            .whs-hero,
            .whs-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .whs-hero-main {
                flex-direction: column;
            }

            .whs-hero-copy h3 {
                font-size: 1.2rem;
            }

            .whs-hero-actions {
                width: 100%;
            }

            .whs-hero-actions .btn {
                flex: 1 1 auto;
            }

            .whs-section-head {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const locale = @json($locale);
            const labels = {!! json_encode($jsLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

            const countrySelect = document.getElementById('country');
    const governorateSelect = document.getElementById('governorate');
    const citySelect = document.getElementById('city');

            const previewScore = document.getElementById('previewScore');
            const previewScoreBar = document.getElementById('previewScoreBar');
            const previewMain = document.getElementById('previewMain');
            const previewName = document.getElementById('previewName');
            const previewPhone = document.getElementById('previewPhone');
            const previewLocation = document.getElementById('previewLocation');
            const previewAddress = document.getElementById('previewAddress');

            function getInput(name) {
                return document.querySelector(`[name="${name}"]`);
            }

            function valueOf(name) {
                const input = getInput(name);
                return input && input.value ? input.value.trim() : '';
            }

            function selectedText(select) {
                if (!select || !select.value) {
                    return '';
                }

                return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text.trim() : '';
            }

            function calculateScore() {
                const checks = [
                    valueOf('name'),
                    valueOf('phone'),
                    valueOf('country_id'),
                    valueOf('governorate_id'),
                    valueOf('city_id'),
                    valueOf('street_name'),
                    valueOf('building'),
                    valueOf('floor'),
                    valueOf('landmark'),
                ];

                return Math.round((checks.filter(Boolean).length / checks.length) * 100);
            }

            function updatePreview() {
                const score = calculateScore();

                if (previewScore) {
                    previewScore.textContent = `${score}%`;
                }

                if (previewScoreBar) {
                    previewScoreBar.style.width = `${score}%`;
                }

                if (previewName) {
                    previewName.textContent = valueOf('name') || labels.warehouseName;
                }

                if (previewPhone) {
                    previewPhone.textContent = valueOf('phone') || labels.phonePlaceholder;
                }

                const locationParts = [
                    selectedText(countrySelect),
                    selectedText(governorateSelect),
                    selectedText(citySelect),
                ].filter(Boolean);

                if (previewLocation) {
                    previewLocation.textContent = locationParts.length
                        ? locationParts.join(', ')
                        : labels.locationPlaceholder;
                }

                const addressParts = [
                    valueOf('street_name'),
                    valueOf('building') ? `${labels.building}: ${valueOf('building')}` : '',
                    valueOf('floor') ? `${labels.floor}: ${valueOf('floor')}` : '',
                    valueOf('landmark'),
                ].filter(Boolean);

                if (previewAddress) {
                    previewAddress.textContent = addressParts.length
                        ? addressParts.join(' - ')
                        : labels.addressPlaceholder;
                }

                const mainCheckbox = document.getElementById('is_main');
                const isMain = mainCheckbox ? mainCheckbox.checked : false;

                if (previewMain) {
                    previewMain.textContent = isMain ? labels.mainWarehouse : labels.regularWarehouse;
                    previewMain.classList.toggle('is-main', isMain);
                }
            }

            function resetSelect(select, placeholder, disabled = true) {
                if (!select) return;

                select.innerHTML = `<option value="">${placeholder}</option>`;
                select.disabled = disabled;
            }

            function fillSelect(select, placeholder, rows) {
                resetSelect(select, placeholder, false);

                rows.forEach(function (row) {
                    const name = row[`name_${locale}`] || row.name_en || row.name_ar || `#${row.id}`;
                    select.innerHTML += `<option value="${row.id}">${name}</option>`;
                });
            }

            function fetchJson(url) {
                return fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function (res) {
                    if (!res.ok) {
                        throw new Error('Request failed');
                    }

                    return res.json();
                });
            }

            if (countrySelect && governorateSelect && citySelect) {
                countrySelect.addEventListener('change', function () {
                    const countryId = this.value;

                    resetSelect(governorateSelect, labels.loading, true);
                    resetSelect(citySelect, labels.selectCity, true);

                    if (!countryId) {
                        resetSelect(governorateSelect, labels.selectGovernorate, true);
                        updatePreview();
                        return;
                    }

                    fetchJson(`/admin/settings/get-states/${countryId}`)
                        .then(function (data) {
                            fillSelect(governorateSelect, labels.selectGovernorate, data);
                        })
                        .catch(function () {
                            resetSelect(governorateSelect, labels.selectGovernorate, true);
                        })
                        .finally(updatePreview);
                });

                governorateSelect.addEventListener('change', function () {
                    const governorateId = this.value;

                    resetSelect(citySelect, labels.loading, true);

                    if (!governorateId) {
                        resetSelect(citySelect, labels.selectCity, true);
                        updatePreview();
                        return;
                    }

                    fetchJson(`/admin/settings/get-cities/${governorateId}`)
                        .then(function (data) {
                            fillSelect(citySelect, labels.selectCity, data);
                        })
                        .catch(function () {
                            resetSelect(citySelect, labels.selectCity, true);
                        })
                        .finally(updatePreview);
                });

                citySelect.addEventListener('change', updatePreview);
            }

            document.querySelectorAll('[data-preview]').forEach(function (input) {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            updatePreview();
        });
    </script>
@endsection
