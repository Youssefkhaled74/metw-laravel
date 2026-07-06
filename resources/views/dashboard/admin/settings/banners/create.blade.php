@extends('layouts.admin')

@section('title', __('admin-dashboard.create_banner'))
@section('page-title', __('admin-dashboard.create_banner'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $placeholderImage = asset('images/placeholder.png');

    $jsLabels = [
        'noImage' => $text('No image selected', 'لم يتم اختيار صورة'),
        'newImage' => $text('New image selected', 'تم اختيار صورة جديدة'),
        'active' => __('admin-dashboard.active'),
        'inactive' => $text('Inactive', 'غير مفعل'),
        'placeholder' => $text('Preview image', 'معاينة الصورة'),
    ];
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.settings.banners.index') }}">{{ __('admin-dashboard.banners') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_banner') }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.settings.banners.index') }}" class="btn bfc-light-btn">
        <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
        {{ __('admin-dashboard.back_to_list') }}
    </a>
@endsection

@section('content')
    <div class="bfc-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <form
            action="{{ route('admin.settings.banners.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="bannerCreateForm"
        >
            @csrf

            <section class="bfc-hero">
                <div class="bfc-hero-main">
                    <div class="bfc-hero-icon">
                        <i class="fas fa-image"></i>
                    </div>

                    <div class="bfc-hero-copy">
                        <span class="bfc-chip">
                            {{ $text('Banner setup', 'إعداد البانر') }}
                        </span>

                        <h3>{{ __('admin-dashboard.create_banner') }}</h3>

                        <p>
                            {{ $text(
                                'Create a new banner, upload its image, preview it instantly, and choose whether it should be active.',
                                'أنشئ بانر جديد وارفع الصورة وعاينها فورًا وحدد هل سيكون مفعّلًا أم لا.'
                            ) }}
                        </p>

                        <div class="bfc-meta">
                            <span>
                                <i class="fas fa-plus"></i>
                                {{ $text('New banner', 'بانر جديد') }}
                            </span>

                            <span id="bannerStatusMeta" class="{{ old('is_active', true) ? 'is-active' : 'is-inactive' }}">
                                <i class="fas fa-circle"></i>
                                {{ old('is_active', true) ? __('admin-dashboard.active') : $text('Inactive', 'غير مفعل') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bfc-hero-actions">
                    <a href="{{ route('admin.settings.banners.index') }}" class="btn bfc-light-btn">
                        {{ $text('Cancel', 'إلغاء') }}
                    </a>

                    <button type="submit" class="btn bfc-primary-btn">
                        <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.create_banner_button') }}
                    </button>
                </div>
            </section>

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <section class="bfc-card">
                        <div class="bfc-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.banner_details') }}</h5>
                                <p>{{ __('admin-dashboard.recommended_size') }}</p>
                            </div>

                            <span class="bfc-required-pill">
                                {{ $text('Required image', 'الصورة مطلوبة') }}
                            </span>
                        </div>

                        <div class="bfc-upload-box">
                            <div class="bfc-upload-icon">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>

                            <div class="bfc-upload-copy">
                                <label for="image" class="bfc-label">
                                    {{ __('admin-dashboard.banner_image') }}
                                    <span>*</span>
                                </label>

                                <p>
                                    {{ $text(
                                        'Choose a clear banner image. The preview will update immediately after selection.',
                                        'اختر صورة بانر واضحة. ستتحدث المعاينة مباشرة بعد الاختيار.'
                                    ) }}
                                    {{ __('admin-dashboard.recommended_size') }}
                                </p>

                                <input
                                    type="file"
                                    class="form-control bfc-control @error('image') is-invalid @enderror"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                    required
                                >

                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small id="selectedFileName" class="bfc-selected-file">
                                    {{ $text('No image selected yet.', 'لم يتم اختيار صورة بعد.') }}
                                </small>
                            </div>
                        </div>
                    </section>

                    {{-- 
                    لو هترجع حقل الرابط، فك الكومنت ده وتأكد إن controller بيحفظ link.

                    <section class="bfc-card">
                        <div class="bfc-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.link_url') }}</h5>
                                <p>{{ $text('Optional destination URL for this banner.', 'رابط اختياري يفتح عند الضغط على البانر.') }}</p>
                            </div>
                        </div>

                        <label for="link" class="bfc-label">{{ __('admin-dashboard.link_url') }}</label>
                        <input
                            type="url"
                            class="form-control bfc-control @error('link') is-invalid @enderror"
                            id="link"
                            name="link"
                            value="{{ old('link') }}"
                            placeholder="{{ __('admin-dashboard.link_url_placeholder') }}"
                        >

                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </section>
                    --}}

                    <section class="bfc-card">
                        <div class="bfc-switch-box">
                            <div>
                                <strong>{{ __('admin-dashboard.active') }}</strong>
                                <small>
                                    {{ $text(
                                        'Enable this banner immediately after saving.',
                                        'فعّل هذا البانر مباشرة بعد الحفظ.'
                                    ) }}
                                </small>
                            </div>

                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                >
                            </div>
                        </div>

                        @error('is_active')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </section>
                </div>

                <div class="col-12 col-xl-4">
                    <aside class="bfc-card bfc-sticky">
                        <div class="bfc-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.preview') }}</h5>
                                <p>
                                    {{ $text(
                                        'Preview the banner before creating it.',
                                        'عاين البانر قبل إنشائه.'
                                    ) }}
                                </p>
                            </div>
                        </div>

                        <div class="bfc-preview" id="imagePreview">
                            <img
                                src="{{ $placeholderImage }}"
                                alt="{{ __('admin-dashboard.preview') }}"
                                id="bannerPreviewImage"
                            >
                        </div>

                        <div class="bfc-preview-info">
                            <div>
                                <span>{{ $text('Image source', 'مصدر الصورة') }}</span>
                                <strong id="previewSourceText">
                                    {{ $text('Placeholder image', 'صورة افتراضية') }}
                                </strong>
                            </div>

                            <div>
                                <span>{{ __('admin-dashboard.status') }}</span>
                                <strong id="previewStatusText">
                                    {{ old('is_active', true) ? __('admin-dashboard.active') : $text('Inactive', 'غير مفعل') }}
                                </strong>
                            </div>
                        </div>

                        <div class="bfc-guide">
                            <div class="bfc-guide-item">
                                <i class="fas fa-image"></i>
                                <div>
                                    <strong>{{ $text('Use a clear banner', 'استخدم بانر واضح') }}</strong>
                                    <small>{{ $text('Avoid low quality or stretched images.', 'تجنب الصور الضعيفة أو المشدودة.') }}</small>
                                </div>
                            </div>

                            <div class="bfc-guide-item">
                                <i class="fas fa-mobile-screen"></i>
                                <div>
                                    <strong>{{ $text('Check responsiveness', 'راجع التجاوب') }}</strong>
                                    <small>{{ $text('The image should work well on mobile and desktop.', 'الصورة لازم تكون مناسبة للموبايل والديسكتوب.') }}</small>
                                </div>
                            </div>

                            <div class="bfc-guide-item">
                                <i class="fas fa-toggle-on"></i>
                                <div>
                                    <strong>{{ $text('Confirm activation', 'تأكد من التفعيل') }}</strong>
                                    <small>{{ $text('Inactive banners may not appear publicly.', 'البانرات غير المفعلة قد لا تظهر للعامة.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="bfc-side-actions">
                            <button type="submit" class="btn bfc-primary-btn w-100">
                                <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ __('admin-dashboard.create_banner_button') }}
                            </button>

                            <a href="{{ route('admin.settings.banners.index') }}" class="btn bfc-light-btn w-100">
                                {{ __('admin-dashboard.back_to_list') }}
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </form>
    </div>

    <style data-page-style>
        .bfc-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .bfc-hero,
        .bfc-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .bfc-hero {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .bfc-hero::before {
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

        .bfc-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .bfc-hero-icon {
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

        .bfc-chip {
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

        .bfc-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .bfc-hero-copy p {
            max-width: 850px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .bfc-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .75rem;
        }

        .bfc-meta span {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .78rem;
            font-weight: 900;
        }

        .bfc-meta span.is-active {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .bfc-meta span.is-inactive {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .bfc-hero-actions {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .bfc-card {
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .bfc-sticky {
            position: sticky;
            top: 1rem;
        }

        .bfc-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .bfc-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .bfc-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .bfc-required-pill {
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

        .bfc-upload-box {
            padding: 1rem;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .bfc-upload-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .bfc-upload-copy {
            flex: 1;
            min-width: 0;
        }

        .bfc-label {
            display: block;
            margin-bottom: .42rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 950;
        }

        .bfc-label span {
            color: #dc2626;
        }

        .bfc-upload-copy p {
            margin: 0 0 .75rem;
            color: #64748b;
            font-size: .84rem;
            line-height: 1.7;
        }

        .bfc-control {
            min-height: 44px;
            border-radius: 14px !important;
            border-color: #dbe3ea !important;
            color: #111827 !important;
            background: #fff !important;
            font-size: .9rem;
            font-weight: 700;
            box-shadow: none !important;
        }

        .bfc-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1) !important;
        }

        .bfc-selected-file {
            display: block;
            margin-top: .55rem;
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            line-height: 1.6;
        }

        .bfc-switch-box {
            min-height: 82px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .bfc-switch-box strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .bfc-switch-box small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.6;
        }

        .bfc-switch-box .form-check-input {
            width: 2.85rem;
            height: 1.45rem;
            cursor: pointer;
        }

        .bfc-preview {
            width: 100%;
            min-height: 210px;
            border-radius: 20px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bfc-preview img {
            width: 100%;
            height: auto;
            min-height: 210px;
            max-height: 360px;
            object-fit: cover;
            display: block;
        }

        .bfc-preview-info {
            display: grid;
            gap: .65rem;
            margin-top: 1rem;
        }

        .bfc-preview-info div {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .bfc-preview-info span {
            display: block;
            color: #64748b;
            font-size: .73rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .bfc-preview-info strong {
            display: block;
            color: #111827;
            font-size: .84rem;
            font-weight: 900;
            line-height: 1.6;
        }

        .bfc-guide {
            display: grid;
            gap: .7rem;
            margin-top: 1rem;
        }

        .bfc-guide-item {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            padding: .85rem;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
        }

        .bfc-guide-item i {
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

        .bfc-guide-item strong {
            display: block;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 950;
            margin-bottom: .15rem;
        }

        .bfc-guide-item small {
            display: block;
            color: #475569;
            font-size: .76rem;
            line-height: 1.6;
        }

        .bfc-side-actions {
            display: grid;
            gap: .65rem;
            margin-top: 1rem;
        }

        .bfc-primary-btn,
        .bfc-light-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bfc-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .bfc-primary-btn:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .bfc-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .bfc-light-btn:hover {
            background: #f8fafc !important;
            color: #111827 !important;
        }

        @media (max-width: 1199.98px) {
            .bfc-sticky {
                position: static;
            }

            .bfc-hero {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 767.98px) {
            .bfc-hero,
            .bfc-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .bfc-hero-main,
            .bfc-upload-box {
                flex-direction: column;
            }

            .bfc-hero-copy h3 {
                font-size: 1.2rem;
            }

            .bfc-hero-actions {
                width: 100%;
            }

            .bfc-hero-actions .btn {
                flex: 1 1 auto;
            }

            .bfc-section-head {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <script data-page-script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = {!! json_encode($jsLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};

            const imageInput = document.getElementById('image');
            const previewImage = document.getElementById('bannerPreviewImage');
            const selectedFileName = document.getElementById('selectedFileName');
            const previewSourceText = document.getElementById('previewSourceText');
            const activeSwitch = document.getElementById('is_active');
            const previewStatusText = document.getElementById('previewStatusText');
            const bannerStatusMeta = document.getElementById('bannerStatusMeta');

            if (imageInput) {
                imageInput.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;

                    if (!file) {
                        if (selectedFileName) {
                            selectedFileName.textContent = labels.noImage;
                        }

                        return;
                    }

                    if (selectedFileName) {
                        selectedFileName.textContent = `${labels.newImage}: ${file.name}`;
                    }

                    if (previewSourceText) {
                        previewSourceText.textContent = labels.newImage;
                    }

                    const reader = new FileReader();

                    reader.onload = function (readerEvent) {
                        if (previewImage) {
                            previewImage.src = readerEvent.target.result;
                            previewImage.classList.remove('d-none');
                        }
                    };

                    reader.readAsDataURL(file);
                });
            }

            function updateStatusPreview() {
                const isActive = activeSwitch ? activeSwitch.checked : false;

                if (previewStatusText) {
                    previewStatusText.textContent = isActive ? labels.active : labels.inactive;
                }

                if (bannerStatusMeta) {
                    bannerStatusMeta.classList.toggle('is-active', isActive);
                    bannerStatusMeta.classList.toggle('is-inactive', !isActive);
                    bannerStatusMeta.innerHTML = `<i class="fas fa-circle"></i> ${isActive ? labels.active : labels.inactive}`;
                }
            }

            if (activeSwitch) {
                activeSwitch.addEventListener('change', updateStatusPreview);
            }

            updateStatusPreview();
        });
    </script>
@endsection