@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_banner'))
@section('page-title', __('admin-dashboard.edit_banner'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $currentImage = $banner->image ? asset($banner->image) : null;

    $jsLabels = [
        'noImage' => $text('No image selected', 'لم يتم اختيار صورة'),
        'newImage' => $text('New image selected', 'تم اختيار صورة جديدة'),
        'active' => __('admin-dashboard.active'),
        'inactive' => $text('Inactive', 'غير مفعل'),
    ];
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.settings.banners.index') }}">{{ __('admin-dashboard.banners') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.edit_banner') }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.settings.banners.index') }}" class="btn bfe-light-btn">
        <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
        {{ __('admin-dashboard.back_to_list') }}
    </a>
@endsection

@section('content')
    <div class="bfe-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <form
            action="{{ route('admin.settings.banners.update', $banner->id) }}"
            method="POST"
            enctype="multipart/form-data"
            id="bannerEditForm"
        >
            @csrf
            @method('PATCH')

            <section class="bfe-hero">
                <div class="bfe-hero-main">
                    <div class="bfe-hero-icon">
                        <i class="fas fa-pen"></i>
                    </div>

                    <div class="bfe-hero-copy">
                        <span class="bfe-chip">
                            {{ $text('Banner setup', 'إعداد البانر') }}
                        </span>

                        <h3>{{ __('admin-dashboard.edit_banner') }}</h3>

                        <p>
                            {{ $text(
                                'Update the banner image and activation status while previewing the result before saving.',
                                'حدّث صورة البانر وحالة التفعيل مع معاينة النتيجة قبل الحفظ.'
                            ) }}
                        </p>

                        <div class="bfe-meta">
                            <span>
                                <i class="fas fa-hashtag"></i>
                                Banner #{{ $banner->id }}
                            </span>

                            <span id="bannerStatusMeta" class="{{ old('is_active', $banner->is_active) ? 'is-active' : 'is-inactive' }}">
                                <i class="fas fa-circle"></i>
                                {{ old('is_active', $banner->is_active) ? __('admin-dashboard.active') : $text('Inactive', 'غير مفعل') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bfe-hero-actions">
                    <a href="{{ route('admin.settings.banners.index') }}" class="btn bfe-light-btn">
                        {{ $text('Cancel', 'إلغاء') }}
                    </a>

                    <button type="submit" class="btn bfe-primary-btn">
                        <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ __('admin-dashboard.update_banner') }}
                    </button>
                </div>
            </section>

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <section class="bfe-card">
                        <div class="bfe-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.edit_banner_details') }}</h5>
                                <p>{{ __('admin-dashboard.recommended_size') }}</p>
                            </div>

                            <span class="bfe-required-pill">
                                {{ $text('Image optional', 'الصورة اختيارية') }}
                            </span>
                        </div>

                        <div class="bfe-upload-box">
                            <div class="bfe-upload-icon">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>

                            <div class="bfe-upload-copy">
                                <label for="image" class="bfe-label">
                                    {{ __('admin-dashboard.banner_image') }}
                                </label>

                                <p>
                                    {{ __('admin-dashboard.keep_current_image') }}
                                    {{ __('admin-dashboard.recommended_size') }}
                                </p>

                                <input
                                    type="file"
                                    class="form-control bfe-control @error('image') is-invalid @enderror"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                >

                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small id="selectedFileName" class="bfe-selected-file">
                                    {{ $text('No new image selected. Current image will be kept.', 'لم يتم اختيار صورة جديدة. سيتم الاحتفاظ بالصورة الحالية.') }}
                                </small>
                            </div>
                        </div>
                    </section>

                    {{-- 
                    لو عايز ترجع حقل الرابط تاني، فك الكومنت ده فقط وتأكد إن controller بيحفظ link.

                    <section class="bfe-card">
                        <div class="bfe-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.link_url') }}</h5>
                                <p>{{ $text('Optional destination URL for this banner.', 'رابط اختياري يفتح عند الضغط على البانر.') }}</p>
                            </div>
                        </div>

                        <label for="link" class="bfe-label">{{ __('admin-dashboard.link_url') }}</label>
                        <input
                            type="url"
                            class="form-control bfe-control @error('link') is-invalid @enderror"
                            id="link"
                            name="link"
                            value="{{ old('link', $banner->link) }}"
                            placeholder="{{ __('admin-dashboard.link_url_placeholder') }}"
                        >

                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </section>
                    --}}

                    <section class="bfe-card">
                        <div class="bfe-switch-box">
                            <div>
                                <strong>{{ __('admin-dashboard.active') }}</strong>
                                <small>
                                    {{ $text(
                                        'Enable this banner to make it available for display.',
                                        'فعّل هذا البانر ليكون متاحًا للعرض.'
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
                                    {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                >
                            </div>
                        </div>

                        @error('is_active')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </section>
                </div>

                <div class="col-12 col-xl-4">
                    <aside class="bfe-card bfe-sticky">
                        <div class="bfe-section-head">
                            <div>
                                <h5>{{ __('admin-dashboard.current_image') }}</h5>
                                <p>
                                    {{ $text(
                                        'Preview the current or newly selected banner image.',
                                        'عاين الصورة الحالية أو الصورة الجديدة المختارة.'
                                    ) }}
                                </p>
                            </div>
                        </div>

                        <div class="bfe-preview" id="imagePreview">
                            @if($currentImage)
                                <img
                                    src="{{ $currentImage }}"
                                    alt="{{ __('admin-dashboard.current_image') }}"
                                    id="bannerPreviewImage"
                                >
                            @else
                                <div class="bfe-preview-empty" id="bannerPreviewEmpty">
                                    <i class="fas fa-image"></i>
                                    <strong>{{ $text('No image available', 'لا توجد صورة') }}</strong>
                                    <span>{{ $text('Upload an image to preview it here.', 'ارفع صورة لمعاينتها هنا.') }}</span>
                                </div>

                                <img
                                    src=""
                                    alt="{{ __('admin-dashboard.current_image') }}"
                                    id="bannerPreviewImage"
                                    class="d-none"
                                >
                            @endif
                        </div>

                        <div class="bfe-preview-info">
                            <div>
                                <span>{{ $text('Banner ID', 'رقم البانر') }}</span>
                                <strong>#{{ $banner->id }}</strong>
                            </div>

                            <div>
                                <span>{{ __('admin-dashboard.status') }}</span>
                                <strong id="previewStatusText">
                                    {{ old('is_active', $banner->is_active) ? __('admin-dashboard.active') : $text('Inactive', 'غير مفعل') }}
                                </strong>
                            </div>

                            <div>
                                <span>{{ $text('Image source', 'مصدر الصورة') }}</span>
                                <strong id="previewSourceText">
                                    {{ $currentImage ? $text('Current image', 'الصورة الحالية') : $text('No image', 'لا توجد صورة') }}
                                </strong>
                            </div>
                        </div>

                        <div class="bfe-guide">
                            <div class="bfe-guide-item">
                                <i class="fas fa-image"></i>
                                <div>
                                    <strong>{{ $text('Use clear visuals', 'استخدم صورة واضحة') }}</strong>
                                    <small>{{ $text('Avoid blurry or cropped banners.', 'تجنب الصور غير الواضحة أو المقصوصة بشكل سيئ.') }}</small>
                                </div>
                            </div>

                            <div class="bfe-guide-item">
                                <i class="fas fa-mobile-screen"></i>
                                <div>
                                    <strong>{{ $text('Check mobile appearance', 'راجع شكل الموبايل') }}</strong>
                                    <small>{{ $text('Banner should look good on small screens.', 'لازم البانر يظهر جيدًا على الشاشات الصغيرة.') }}</small>
                                </div>
                            </div>

                            <div class="bfe-guide-item">
                                <i class="fas fa-toggle-on"></i>
                                <div>
                                    <strong>{{ $text('Confirm status', 'تأكد من الحالة') }}</strong>
                                    <small>{{ $text('Inactive banners may not appear publicly.', 'البانرات غير المفعلة قد لا تظهر للعامة.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="bfe-side-actions">
                            <button type="submit" class="btn bfe-primary-btn w-100">
                                <i class="fas fa-save {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ __('admin-dashboard.update_banner') }}
                            </button>

                            <a href="{{ route('admin.settings.banners.index') }}" class="btn bfe-light-btn w-100">
                                {{ __('admin-dashboard.back_to_list') }}
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </form>
    </div>

    <style data-page-style>
        .bfe-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .bfe-hero,
        .bfe-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .bfe-hero {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            overflow: hidden;
            position: relative;
        }

        .bfe-hero::before {
            content: "";
            position: absolute;
            inset-inline-start: -90px;
            top: -90px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(245, 158, 11, .09);
            pointer-events: none;
        }

        .bfe-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .bfe-hero-icon {
            width: 66px;
            height: 66px;
            border-radius: 24px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
        }

        .bfe-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .75rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            font-size: .78rem;
            font-weight: 900;
        }

        .bfe-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .bfe-hero-copy p {
            max-width: 850px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .bfe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .75rem;
        }

        .bfe-meta span {
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

        .bfe-meta span.is-active {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .bfe-meta span.is-inactive {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .bfe-hero-actions {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .bfe-card {
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .bfe-sticky {
            position: sticky;
            top: 1rem;
        }

        .bfe-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .bfe-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .bfe-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.6;
        }

        .bfe-required-pill {
            display: inline-flex;
            padding: .32rem .7rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: .73rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .bfe-upload-box {
            padding: 1rem;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .bfe-upload-icon {
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

        .bfe-upload-copy {
            flex: 1;
            min-width: 0;
        }

        .bfe-label {
            display: block;
            margin-bottom: .42rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 950;
        }

        .bfe-upload-copy p {
            margin: 0 0 .75rem;
            color: #64748b;
            font-size: .84rem;
            line-height: 1.7;
        }

        .bfe-control {
            min-height: 44px;
            border-radius: 14px !important;
            border-color: #dbe3ea !important;
            color: #111827 !important;
            background: #fff !important;
            font-size: .9rem;
            font-weight: 700;
            box-shadow: none !important;
        }

        .bfe-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1) !important;
        }

        .bfe-selected-file {
            display: block;
            margin-top: .55rem;
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            line-height: 1.6;
        }

        .bfe-switch-box {
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

        .bfe-switch-box strong {
            display: block;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .bfe-switch-box small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.6;
        }

        .bfe-switch-box .form-check-input {
            width: 2.85rem;
            height: 1.45rem;
            cursor: pointer;
        }

        .bfe-preview {
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

        .bfe-preview img {
            width: 100%;
            height: auto;
            min-height: 210px;
            max-height: 360px;
            object-fit: cover;
            display: block;
        }

        .bfe-preview-empty {
            min-height: 210px;
            padding: 1.5rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .35rem;
        }

        .bfe-preview-empty i {
            width: 64px;
            height: 64px;
            border-radius: 22px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            margin-bottom: .35rem;
        }

        .bfe-preview-empty strong {
            color: #111827;
            font-weight: 950;
        }

        .bfe-preview-empty span {
            color: #64748b;
            font-size: .82rem;
        }

        .bfe-preview-info {
            display: grid;
            gap: .65rem;
            margin-top: 1rem;
        }

        .bfe-preview-info div {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .bfe-preview-info span {
            display: block;
            color: #64748b;
            font-size: .73rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .bfe-preview-info strong {
            display: block;
            color: #111827;
            font-size: .84rem;
            font-weight: 900;
            line-height: 1.6;
        }

        .bfe-guide {
            display: grid;
            gap: .7rem;
            margin-top: 1rem;
        }

        .bfe-guide-item {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            padding: .85rem;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
        }

        .bfe-guide-item i {
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

        .bfe-guide-item strong {
            display: block;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 950;
            margin-bottom: .15rem;
        }

        .bfe-guide-item small {
            display: block;
            color: #475569;
            font-size: .76rem;
            line-height: 1.6;
        }

        .bfe-side-actions {
            display: grid;
            gap: .65rem;
            margin-top: 1rem;
        }

        .bfe-primary-btn,
        .bfe-light-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bfe-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .bfe-primary-btn:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        .bfe-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .bfe-light-btn:hover {
            background: #f8fafc !important;
            color: #111827 !important;
        }

        @media (max-width: 1199.98px) {
            .bfe-sticky {
                position: static;
            }

            .bfe-hero {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 767.98px) {
            .bfe-hero,
            .bfe-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .bfe-hero-main,
            .bfe-upload-box {
                flex-direction: column;
            }

            .bfe-hero-copy h3 {
                font-size: 1.2rem;
            }

            .bfe-hero-actions {
                width: 100%;
            }

            .bfe-hero-actions .btn {
                flex: 1 1 auto;
            }

            .bfe-section-head {
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
            const previewEmpty = document.getElementById('bannerPreviewEmpty');
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

                        if (previewEmpty) {
                            previewEmpty.classList.add('d-none');
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