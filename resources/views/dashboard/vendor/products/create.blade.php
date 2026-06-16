@extends('layouts.vendor')

@section('title', __('vendor-dashboard.create_product'))
@section('page-title', __('vendor-dashboard.create_new_product'))

@push('styles')
<style>
    .vendor-product-form-page .section-card {
        border-radius: 14px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .vendor-product-form-page .section-card .card-body {
        padding: 1.5rem 1.75rem;
    }
    .vendor-product-form-page .form-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .vendor-product-form-page .form-section-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .vendor-product-form-page .form-section-title i {
        color: #64748b;
        font-size: 0.9rem;
    }
    .vendor-product-form-page .form-control,
    .vendor-product-form-page .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.85rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .vendor-product-form-page .form-control:focus,
    .vendor-product-form-page .form-select:focus {
        border-color: #fd7e14;
        box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.15);
    }
    .vendor-product-form-page .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .vendor-product-form-page .input-group .form-control {
        border-radius: 0 10px 10px 0;
    }
    .vendor-product-form-page .form-check-input:checked {
        background-color: #fd7e14;
        border-color: #fd7e14;
    }
    .vendor-product-form-page .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.2);
    }
    .vendor-product-form-page .sidebar-sticky {
        position: sticky;
        top: 1rem;
    }
    .vendor-product-form-page .sidebar-block {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
    }
    .vendor-product-form-page .sidebar-block-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .vendor-product-form-page .sidebar-block-title i {
        color: #64748b;
    }
    .vendor-product-form-page .form-actions-bar {
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 0;
        margin-top: 1.5rem;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }
    .vendor-product-form-page .btn-primary {
        border-radius: 10px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
    }
    .vendor-product-form-page .btn-secondary {
        border-radius: 10px;
        padding: 0.5rem 1.5rem;
    }
    .vendor-product-form-page .required-label::after {
        content: " *";
        color: #dc2626;
    }
    .vendor-product-form-page .preview-container img {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .vendor-product-form-page .table {
        border-radius: 10px;
        overflow: hidden;
    }
    .vendor-product-form-page .table th {
        background: #f8fafc;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
    }
    .vendor-product-form-page .table td {
        vertical-align: middle;
    }
    @media (max-width: 991.98px) {
        .vendor-product-form-page .sidebar-sticky { position: static; }
    }
</style>
@endpush

@section('content')
<div class="vendor-product-form-page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card section-card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="form-section">
                                    <div class="form-section-title"><i class="fas fa-info-circle"></i> {{ __('vendor-dashboard.basic_information_english') }}</div>
                                        <div class="mb-3">
                                            <label for="name_en" class="form-label required-label">{{ __('vendor-dashboard.product_name_en') }}</label>
                                            <input type="text" class="form-control @error('translations.en.name') is-invalid @enderror"
                                                id="name_en" name="translations[en][name]" value="{{ old('translations.en.name') }}" required>
                                            @error('translations.en.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="description_en" class="form-label required-label">{{ __('vendor-dashboard.description_en') }}</label>
                                            <textarea class="form-control @error('translations.en.description') is-invalid @enderror"
                                                id="description_en" name="translations[en][description]" rows="4" required>{{ old('translations.en.description') }}</textarea>
                                            @error('translations.en.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    <div class="form-section-title mt-4"><i class="fas fa-language"></i> {{ __('vendor-dashboard.basic_information_arabic') }}</div>
                                        <div class="mb-3">
                                            <label for="name_ar" class="form-label required-label">{{ __('vendor-dashboard.product_name_ar') }}</label>
                                            <input type="text" class="form-control @error('translations.ar.name') is-invalid @enderror"
                                                id="name_ar" name="translations[ar][name]" value="{{ old('translations.ar.name') }}" required>
                                            @error('translations.ar.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="description_ar" class="form-label required-label">{{ __('vendor-dashboard.description_ar') }}</label>
                                            <textarea class="form-control @error('translations.ar.description') is-invalid @enderror"
                                                id="description_ar" name="translations[ar][description]" rows="4" required>{{ old('translations.ar.description') }}</textarea>
                                            @error('translations.ar.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    <div class="form-section-title mt-4"><i class="fas fa-tag"></i> {{ __('vendor-dashboard.price') }} & {{ __('vendor-dashboard.category') }}</div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label required-label">{{ __('vendor-dashboard.price') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ config('settings.currency_symbol', 'EGP') }}</span>
                                                    <input type="number"
                                                        class="form-control @error('price') is-invalid @enderror"
                                                        id="price" name="price" value="{{ old('price') }}"
                                                        step="0.01" min="0" required>
                                                </div>
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                                    <div class="mb-3">
                                                        <label for="final_price" class="form-label">{{ __('vendor-dashboard.final_price_after_discount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">{{ config('settings.currency_symbol', 'EGP') }}</span>
                                                            <input type="number" class="form-control" id="final_price" name="final_price"
                                                                value="{{ old('final_price') }}" step="0.01" min="0" readonly>
                                                        </div>
                                                    </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="main_category_id" class="form-label required-label">{{ __('vendor-dashboard.main_category') }}</label>
                                                <select class="form-select" id="main_category_id" name="main_category_id" required>
                                                    <option value="">{{ __('vendor-dashboard.select_main_category') }}</option>
                                                    @foreach ($mainCategories as $mainCategory)
                                                        <option value="{{ $mainCategory->id }}">
                                                            {{ $mainCategory->name ?? $mainCategory->translation('en')->name ?? $mainCategory->translation('ar')->name ?? 'No Name' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label required-label">{{ __('vendor-dashboard.category') }}</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id" required>
                                            <option value="">{{ __('vendor-dashboard.select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name ?? $category->translation('en')->name ?? $category->translation('ar')->name ?? 'No Name' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Second Main Category & Second Category -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="main_category_id_2" class="form-label">{{ __('vendor-dashboard.second_main_category_optional') }}</label>
                                                <select class="form-select" id="main_category_id_2"
                                                    name="main_category_id_2">
                                                    <option value="">{{ __('vendor-dashboard.select_second_main_category') }}</option>
                                                    @foreach ($mainCategories as $mainCategory)
                                                        <option value="{{ $mainCategory->id }}"
                                                            {{ old('main_category_id_2') == $mainCategory->id ? 'selected' : '' }}>
                                                            {{ $mainCategory->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category_id_2" class="form-label">{{ __('vendor-dashboard.second_category_optional') }}</label>
                                                <select class="form-select @error('category_id_2') is-invalid @enderror"
                                                    id="category_id_2" name="category_id_2">
                                                    <option value="">{{ __('vendor-dashboard.select_second_category') }}</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id_2') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id_2')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Brand -->
                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label required-label">{{ __('vendor-dashboard.brand') }}</label>
                                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                                id="brand_id" name="brand_id" required>
                                            <option value="">{{ __('vendor-dashboard.select_brand') }}</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name_en ?? $brand->name_ar ?? $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Extra Details -->
                                <div class="form-section">
                                    <div class="form-section-title"><i class="fas fa-align-left"></i> {{ __('vendor-dashboard.extra_product_details') }}</div>

                                        <div class="mb-3">
                                            <label for="usage_description" class="form-label required-label">{{ __('vendor-dashboard.usage_description') }}</label>
                                            <textarea class="form-control" id="usage_description" name="usage_description">{{ old('usage_description') }}</textarea>
                                        </div>


                                        <div class="mb-3">
                                            <label for="parts_description" class="form-label">{{ __('vendor-dashboard.parts_description') }}</label>
                                            <textarea class="form-control" id="parts_description" name="parts_description">{{ old('parts_description') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="material_description" class="form-label">{{ __('vendor-dashboard.material_description') }}</label>
                                            <textarea class="form-control" id="material_description" name="material_description">{{ old('material_description') }}</textarea>
                                        </div>

                                        {{-- <div class="row">
                                            <div class="col-md-4">
                                                <label for="dimensions" class="form-label">{{ __('vendor-dashboard.dimensions') }}</label>
                                                <input type="text" class="form-control" id="dimensions" name="dimensions" value="{{ old('dimensions') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="weight" class="form-label">{{ __('vendor-dashboard.weight') }}</label>
                                                <input type="text" class="form-control" id="weight" name="weight" value="{{ old('weight') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="volume" class="form-label">{{ __('vendor-dashboard.volume') }}</label>
                                                <input type="text" class="form-control" id="volume" name="volume" value="{{ old('volume') }}">
                                            </div>
                                        </div> --}}

                                    </div>

                                    <!-- Additional Product Fields -->
                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-info-circle"></i> {{ __('vendor-dashboard.additional_product_information') }}</div>

                                        <div class="mb-3">
                                            <label for="features" class="form-label">{{ __('vendor-dashboard.features') }}</label>
                                            <textarea class="form-control" id="features" name="features">{{ old('features') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="product_info" class="form-label">{{ __('vendor-dashboard.product_info') }}</label>
                                            <textarea class="form-control" id="product_info" name="product_info">{{ old('product_info') }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="origin_country_select" class="form-label required-label">{{ __('vendor-dashboard.country_of_origin') }}</label>
                                                <select id="origin_country_select" class="form-select">
                                                    <option value="">{{ __('vendor-dashboard.select_country') }}</option>
                                                </select>
                                                <input type="hidden" id="origin_country" name="origin_country" value="{{ old('origin_country') }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="manufacturer" class="form-label">{{ __('vendor-dashboard.manufacturer') }}</label>
                                                <input type="text" class="form-control" id="manufacturer" name="manufacturer"
                                                    value="{{ old('manufacturer') }}">
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="model" class="form-label">{{ __('vendor-dashboard.model') }}</label>
                                                <input type="text" class="form-control" id="model" name="model" value="{{ old('model') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="expiry_period" class="form-label">{{ __('vendor-dashboard.expiry_period') }}</label>
                                                <input type="text" class="form-control" id="expiry_period" name="expiry_period"
                                                    value="{{ old('expiry_period') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-layer-group"></i> {{ __('vendor-dashboard.additional_product_information') }}</div>
                                        <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="subcategory_level1" class="form-label">{{ __('vendor-dashboard.classification_first_subsection') }}</label>
                                            <input type="text" class="form-control" id="subcategories_level1" name="subcategories_level1"
                                                    value="{{ old('subcategories_level1') }}">
                                        </div>

                                        <!-- التصنيف داخل القسم الفرعي الثاني -->
                                        <div class="col-md-6 mb-3">
                                            <label for="subcategory_level2" class="form-label">{{ __('vendor-dashboard.classification_second_subsection') }}</label>
                                            <input type="text" class="form-control" id="subcategory_level2" name="subcategory_level2"
                                                                                                value="{{ old('subcategory_level2') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- الألوان المتوفرة -->
                                        {{-- <div class="col-md-6 mb-3">
                                            <label for="colors" class="form-label"> Available colors <span style="color:red">*</span></label>
                                            <select id="colors" name="colors[]" class="form-select" multiple>
                                                @foreach($colors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Select one or more colors from the color selection box. </small>
                                        </div> --}}

                                        <!-- تاريخ نهاية تطبيق نسبة الخصم -->
                                        <div class="col-md-6 mb-3">
                                            <label for="auto_discount_end_date" class="form-label">{{ __('vendor-dashboard.discount_end_date') }}</label>
                                            <input type="date" class="form-control" id="auto_discount_end_date" name="auto_discount_end_date"
                                                value="{{ old('auto_discount_end_date') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- التوصيل المجاني -->
                                        <div class="col-md-6 mb-3">
                                            <label for="free_shipping" class="form-label">{{ __('vendor-dashboard.free_delivery_eligible') }}</label>
                                            <select id="free_shipping" name="free_shipping" class="form-select">
                                                <option value="0">{{ __('vendor-dashboard.not_available') }}</option>
                                                <option value="available">{{ __('vendor-dashboard.available') }}</option>
                                                <option value="price">{{ __('vendor-dashboard.based_on_specific_price') }}</option>
                                            </select>
                                        </div>

                                        <!-- سعر التوصيل المجاني -->
                                        <div class="col-md-6 mb-3" id="free_shipping_price_container" style="display:none;">
                                            <label for="free_shipping_price" class="form-label">{{ __('vendor-dashboard.free_shipping_price') }}</label>
                                            <input type="number" class="form-control" id="free_shipping_price" name="free_shipping_price"
                                                placeholder="{{ __('vendor-dashboard.enter_price_eligible_free_shipping') }}" step="0.01">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- أبعاد الشحنة -->
                                        <div class="col-md-4 mb-3">
                                            <label for="package_length" class="form-label">{{ __('vendor-dashboard.length_m') }}</label>
                                            <input type="number" class="form-control" id="package_length" name="package_length" step="0.01" min="0">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="package_width" class="form-label">{{ __('vendor-dashboard.width_m') }}</label>
                                            <input type="number" class="form-control" id="package_width" name="package_width" step="0.01" min="0">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="package_height" class="form-label">{{ __('vendor-dashboard.height_m') }}</label>
                                            <input type="number" class="form-control" id="package_height" name="package_height" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <!-- وزن الشحنة -->
                                        <label for="package_weight" class="form-label">{{ __('vendor-dashboard.shipment_weight_kg') }}</label>
                                        <input type="number" class="form-control" id="package_weight" name="package_weight" step="0.01" min="0">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('vendor-dashboard.storage_shipping_conditions') }}</label>

                                        <div class="row">
                                            @php
                                                $storageConditions = [
                                                    'Explosive' => 'متفجر',
                                                    'Flammable' => 'قابل للاشتعال',
                                                    'Fragile' => 'قابل للكسر',
                                                    'Stack and transport according to arrow' => 'يُرص ويُنقل حسب اتجاه السهم',
                                                    'Do not expose to sunlight' => 'عدم التعرض لأشعة الشمس',
                                                    'Do not expose to heat' => 'عدم التعرض للحرارة',
                                                    'Do not expose to humidity' => 'عدم التعرض للرطوبة',
                                                    'Do not expose to high liquids' => 'عدم التعرض للسوائل العالية',
                                                    'Store frozen' => 'يُحفظ مجمّدًا',
                                                    'Store refrigerated' => 'يُحفظ مبردًا',
                                                ];

                                                $isArabic = app()->getLocale() === 'ar';
                                            @endphp

                                            @foreach($storageConditions as $en => $ar)
                                                @php
                                                    $label = $isArabic ? $ar : $en;
                                                @endphp

                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            name="storage_conditions[]"
                                                            value="{{ $en }}"
                                                            id="cond_{{ $loop->index }}">

                                                        <label class="form-check-label" for="cond_{{ $loop->index }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label for="delivery_options" class="form-label">
                                                {{ __('vendor-dashboard.delivery_options_governorate') }}
                                            </label>

                                            <select id="delivery_options"
                                                    name="delivery_options[]"
                                                    class="form-select select2 @error('delivery_options') is-invalid @enderror"
                                                    multiple>

                                                @php
                                                    // English → Arabic mapping
                                                    $deliveryOptions = [
                                                        'Cities' => 'المدن',
                                                        'Villages near cities' => 'القرى القريبة من المدن',
                                                        'Villages far from cities' => 'القرى البعيدة عن المدن',
                                                    ];

                                                    // Language detection
                                                    $isArabic = app()->getLocale() === 'ar';

                                                    // Old input (for create form)
                                                    $selectedOptions = old('delivery_options', []);
                                                @endphp

                                                @foreach ($deliveryOptions as $en => $ar)
                                                    <option value="{{ $en }}" {{ in_array($en, $selectedOptions) ? 'selected' : '' }}>
                                                        {{ $isArabic ? $ar : $en }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('delivery_options')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>



                                    <!-- Discount Section -->
                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-percent"></i> {{ __('vendor-dashboard.discount') }}</div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="discount_percentage" class="form-label">{{ __('vendor-dashboard.discount_percentage') }}</label>
                                                <input type="number" class="form-control" name="discount_percentage" id="discount_percentage"
                                                    value="{{ old('discount_percentage') }}" min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="discount_start" class="form-label required-label">{{ __('vendor-dashboard.discount_start') }}</label>
                                                <input type="date" class="form-control" name="discount_start" id="discount_start"
                                                    value="{{ old('discount_start') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="discount_end" class="form-label required-label">{{ __('vendor-dashboard.discount_end') }}</label>
                                                <input type="date" class="form-control" name="discount_end" id="discount_end"
                                                    value="{{ old('discount_end') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deposit Section -->
                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-money-bill-wave"></i> {{ __('vendor-dashboard.deposit') }}</div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="has_deposit"
                                                    name="has_deposit" value="1"
                                                    {{ old('has_deposit') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="has_deposit">{{ __('vendor-dashboard.enable_deposit') }}</label>
                                            </div>
                                            <small class="form-text text-muted">{{ __('vendor-dashboard.deposit_help') }}</small>
                                        </div>

                                        <div id="deposit_details_container" style="display: {{ old('has_deposit') ? 'block' : 'none' }};">
                                            <div class="row">
                                                {{-- <div class="col-md-6 mb-3">
                                                    <label for="deposit_amount" class="form-label">{{ __('vendor-dashboard.deposit_amount') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="deposit_amount" name="deposit_amount"
                                                            value="{{ old('deposit_amount') }}" step="0.01" min="0"
                                                            placeholder="{{ __('vendor-dashboard.enter_deposit_amount') }}">
                                                    </div>
                                                    <small class="form-text text-muted">{{ __('vendor-dashboard.deposit_amount_help') }}</small>
                                                </div> --}}

                                                <div class="col-md-6 mb-3">
                                                    <label for="deposit_percentage" class="form-label">{{ __('vendor-dashboard.deposit_percentage') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="deposit_percentage" name="deposit_percentage"
                                                            value="{{ old('deposit_percentage') }}" step="0.01" min="0" max="100"
                                                            placeholder="{{ __('vendor-dashboard.enter_deposit_percentage') }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <small class="form-text text-muted">{{ __('vendor-dashboard.deposit_percentage_help') }}</small>
                                                </div>
                                            </div>

                                            {{-- <div class="mb-3">
                                                <label for="final_price_after_deposit_display" class="form-label">{{ __('vendor-dashboard.final_price_after_deposit') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="final_price_after_deposit_display" readonly
                                                        value="{{ old('final_price_after_deposit') }}" step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">{{ __('vendor-dashboard.final_price_after_deposit_help') }}</small>
                                                <!-- Hidden field for actual value -->
                                                <input type="hidden" id="final_price_after_deposit" name="final_price_after_deposit"
                                                    value="{{ old('final_price_after_deposit') }}">
                                            </div> --}}
                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-truck"></i> {{ __('vendor-dashboard.shipping') }}</div>

    <!-- Branch Selection -->
    <div class="mb-3">
        <label for="branch_id" class="form-label required-label">{{ __('vendor-dashboard.branch') }}</label>
        <select class="form-select @error('branch_id') is-invalid @enderror"
                id="branch_id"
                name="branch_id"
                required>
            <option value="">{{ __('vendor-dashboard.select_branch') }}</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }} - {{ $branch->full_address }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">{{ __('vendor-dashboard.branch_help') }}</small>
        @error('branch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="shipment_type" class="form-label">{{ __('vendor-dashboard.shipment_type') }}</label>
        <select class="form-select" id="shipment_type" name="shipment_type" required>
            <option value="">{{ __('vendor-dashboard.select_shipment_type') }}</option>
            @foreach ($consignment_types as $consignment_type)
                <option value="{{ $consignment_type->id }}">
                    {{ $consignment_type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="shipment_description" class="form-label required-label">{{ __('vendor-dashboard.shipment_description') }}</label>
        <textarea class="form-control" id="shipment_description" name="shipment_description">{{ old('shipment_description') }}</textarea>
    </div>
</div>

                                    </div>

                                    <!-- Package Information Section -->
                                <div class="form-section">
                                    <div class="form-section-title"><i class="fas fa-box"></i> {{ __('vendor-dashboard.package_information') }}</div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="pieces_per_package" class="form-label required-label">
                                                {{ __('vendor-dashboard.pieces_per_package') }}
                                            </label>
                                            <input type="number"
                                                class="form-control @error('pieces_per_package') is-invalid @enderror"
                                                id="pieces_per_package"
                                                name="pieces_per_package"
                                                value="{{ old('pieces_per_package') }}"
                                                min="1"
                                                required>
                                            <small class="form-text text-muted">
                                                {{ __('vendor-dashboard.pieces_per_package_help') }}
                                            </small>
                                            @error('pieces_per_package')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="piece_type" class="form-label">{{ __('vendor-dashboard.piece_type') }}</label>
                                            <select class="form-select @error('piece_type') is-invalid @enderror"
                                                    id="piece_type" name="piece_type">
                                                <option value="">{{ __('vendor-dashboard.select_piece_type') }}</option>
                                                <option value="small" {{ old('piece_type') == 'small' ? 'selected' : '' }}>
                                                    {{ __('vendor-dashboard.small') }}
                                                </option>
                                                <option value="medium" {{ old('piece_type') == 'medium' ? 'selected' : '' }}>
                                                    {{ __('vendor-dashboard.medium') }}
                                                </option>
                                                <option value="large" {{ old('piece_type') == 'large' ? 'selected' : '' }}>
                                                    {{ __('vendor-dashboard.large') }}
                                                </option>
                                                <option value="xlarge" {{ old('piece_type') == 'xlarge' ? 'selected' : '' }}>
                                                    {{ __('vendor-dashboard.xlarge') }}
                                            </select>
                                            @error('piece_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                    <!-- Return Policy -->
                                    <div class="form-section">
                                        <div class="form-section-title"><i class="fas fa-undo"></i> {{ __('vendor-dashboard.return_policy') }}</div>

                                        <!-- Is Returnable Checkbox -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="is_returnable"
                                                    name="is_returnable" value="1"
                                                    {{ old('is_returnable') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_returnable">{{ __('vendor-dashboard.product_returnable') }}</label>
                                            </div>
                                        </div>

                                        <!-- Return Details Container -->
                                        <div id="return_details_container" style="display: none;">
                                            <div class="row">
                                                <!-- Return Validity (Days) -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="return_validity" class="form-label">{{ __('vendor-dashboard.return_validity_days') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="return_validity"
                                                            name="return_validity" value="{{ old('return_validity', 7) }}"
                                                            min="1" max="365" placeholder="7">
                                                        <span class="input-group-text">{{ __('vendor-dashboard.days') }}</span>
                                                    </div>
                                                    <small class="form-text text-muted">{{ __('vendor-dashboard.return_validity_help') }}</small>
                                                </div>

                                                <!-- Return Fee -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="return_fee" class="form-label">{{ __('vendor-dashboard.return_fee') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ config('settings.currency_symbol', 'EGP') }}</span>
                                                        <input type="number" class="form-control" id="return_fee" name="return_fee"
                                                            value="{{ old('return_fee', 0) }}" step="0.01" min="0"
                                                            placeholder="{{ __('vendor-dashboard.enter_return_fee') }}">
                                                    </div>
                                                    <small class="form-text text-muted">{{ __('vendor-dashboard.return_fee_help') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock -->
                                    <div class="mb-3" id="stock-input-group">
                                        <label for="stock" class="form-label">{{ __('vendor-dashboard.stock') }}</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                            id="stock" name="stock" value="{{ old('stock', 0) }}" min="0">
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar Section -->
                            <div class="col-md-4">
                                <div class="sidebar-sticky">
                                <!-- Images -->
                                <div class="sidebar-block">
                                    <div class="sidebar-block-title"><i class="fas fa-images"></i> {{ __('vendor-dashboard.product_images') }}</div>
                                    <div class="mb-3">
                                        <label for="images" class="form-label required-label">{{ __('vendor-dashboard.upload_images') }}</label>
                                        <input type="file"
                                            class="form-control @error('images.*') is-invalid @enderror" id="images"
                                            name="images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">{{ __('vendor-dashboard.max_5_images_allowed') }}</small>
                                        @error('images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="image-preview" class="row mt-3"></div>
                                </div>

                                <!-- Extra Images -->
                                <div class="sidebar-block">
                                    <div class="sidebar-block-title"><i class="fas fa-image"></i> {{ __('vendor-dashboard.extra_images') }}</div>
                                    <div class="mb-3">
                                        <label for="extra_images" class="form-label">{{ __('vendor-dashboard.upload_extra_images') }}</label>
                                        <input type="file"
                                            class="form-control @error('extra_images.*') is-invalid @enderror"
                                            id="extra_images" name="extra_images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">{{ __('vendor-dashboard.optional_additional_images') }}</small>
                                        @error('extra_images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="extra-image-preview" class="row mt-3"></div>
                                </div>

                                <!-- Color Images -->
                                {{-- <div class="mb-4">
                                    <h5>Color Images</h5>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="color_images" class="form-label">Upload Color Images</label>
                                        <input type="file"
                                            class="form-control @error('color_images.*') is-invalid @enderror"
                                            id="color_images" name="color_images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">Optional. Images showing product colors.</small>
                                        @error('color_images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="color-image-preview" class="row mt-3"></div>
                                </div> --}}

                                <!-- Videos -->
                                <div class="sidebar-block">
                                    <div class="sidebar-block-title"><i class="fas fa-video"></i> {{ __('vendor-dashboard.product_videos') }}</div>
                                    <div class="mb-3">
                                        <label for="videos" class="form-label">{{ __('vendor-dashboard.upload_videos') }}</label>
                                        <input type="file"
                                            class="form-control @error('videos.*') is-invalid @enderror" id="videos"
                                            name="videos[]" multiple accept="video/*">
                                        <small class="form-text text-muted">{{ __('vendor-dashboard.video_formats_size_limit') }}</small>
                                        @error('videos.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="sidebar-block">
                                    <div class="sidebar-block-title"><i class="fas fa-toggle-on"></i> {{ __('vendor-dashboard.product_status') }}</div>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_active"
                                            name="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('vendor-dashboard.active') }}</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="requires_delivery_otp"
                                            name="requires_delivery_otp" value="1"
                                            {{ old('requires_delivery_otp', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_delivery_otp">{{ __('vendor-dashboard.requires_delivery_otp') }}</label>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- Variants -->
                        <div class="form-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-section-title mb-0"><i class="fas fa-palette"></i> {{ __('vendor-dashboard.variants') }}</div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="has_variants" name="has_variants" value="1">
                                    <label class="form-check-label" for="has_variants">{{ __('vendor-dashboard.use_variants') }}</label>
                                </div>
                            </div>
                            <div id="variants-section" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="variants-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('vendor-dashboard.color') }}</th>
                                                <th>{{ __('vendor-dashboard.size') }}</th>
                                                <th>{{ __('vendor-dashboard.price') }}</th>
                                                <th>{{ __('vendor-dashboard.stock') }}</th>
                                                <th>{{ __('vendor-dashboard.color_images') }}</th>
                                                <th>{{ __('vendor-dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="display: none;">
                                            <td>
                                                <select name="variants[0][color_id]" class="form-select">
                                                    <option value="">{{ __('vendor-dashboard.none') }}</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="variants[0][size_id]" class="form-select">
                                                    <option value="">{{ __('vendor-dashboard.none') }}</option>
                                                    @foreach ($sizes as $size)
                                                        <option value="{{ $size->id }}">{{ $size->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="variants[0][price]" class="form-control"
                                                    step="0.01" min="0" placeholder="Auto = main price">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[0][stock]" class="form-control"
                                                    step="1" min="0" value="0">
                                            </td>
                                            <td>
                                                <div class="color-images-container">
                                                    <input type="file"
                                                        class="form-control color-images-input"
                                                        name="variants[0][color_images][]"
                                                        multiple accept="image/*">
                                                    <div class="preview-container d-flex flex-wrap mt-2 gap-2"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-variant" disabled>{{ __('vendor-dashboard.remove') }}</button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary" id="add-variant">{{ __('vendor-dashboard.add_variant') }}</button>
                            </div>
                        </div>

                        <div class="form-actions-bar text-end">
                            <a href="{{ route('vendor.products') }}" class="btn btn-secondary">{{ __('vendor-dashboard.cancel') }}</a>
                            <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-save me-1"></i>{{ __('vendor-dashboard.create_product') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function handleFilePreview(inputId, previewId, type = 'image') {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        let filesArray = [];

        input.addEventListener('change', function(e) {
            // Add newly selected files to array
            filesArray = [...filesArray, ...e.target.files];
            renderPreview();
            // reset input so user can reselect same file later
            // input.value = "";
        });

        function renderPreview() {
            preview.innerHTML = '';

            filesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'col-6 mb-3 position-relative';

                    let content = '';
                    if (type === 'image') {
                        content = `<img src="${ev.target.result}" class="img-thumbnail" alt="Preview">`;
                    } else {
                        content = `
                            <video controls width="100%" class="img-thumbnail">
                                <source src="${ev.target.result}" type="${file.type}">
                                Your browser does not support the video tag.
                            </video>`;
                    }

                    div.innerHTML = `
                        ${content}
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">&times;</button>
                    `;

                    // Remove button
                    div.querySelector('button').addEventListener('click', function() {
                        filesArray.splice(index, 1); // remove file from array
                        renderPreview(); // re-render
                    });

                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });

            // update the input.files with the current filesArray
            const dataTransfer = new DataTransfer();
            filesArray.forEach(f => dataTransfer.items.add(f));
            input.files = dataTransfer.files;
        }
    }

    // Preview with remove option
    handleFilePreview('images', 'image-preview', 'image');
    handleFilePreview('extra_images', 'extra-image-preview', 'image');
    // handleFilePreview('color_images', 'color-image-preview', 'image');
    handleFilePreview('videos', 'video-preview', 'video');

    // Variants dynamic rows
    (function() {
        const table = document.getElementById('variants-table').getElementsByTagName('tbody')[0];
        const addBtn = document.getElementById('add-variant');
        let rowIndex = 1;
        const colors = @json($colors->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
        const sizes = @json($sizes->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));

        addBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="variants[${rowIndex}][color_id]" class="form-select">
                        <option value="">None</option>
                        ${colors.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                <select name="variants[${rowIndex}][size_id]" class="form-select">
                    <option value="">None</option>
                    @foreach ($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->title }}</option>
                    @endforeach
                </select>
                </td>
                <td>
                    <input type="number" name="variants[${rowIndex}][price]" class="form-control" step="0.01" min="0" placeholder="Auto = main price">
                </td>
                <td>
                    <input type="number" name="variants[${rowIndex}][stock]" class="form-control" step="1" min="0" value="0">
                </td>
                <td>
                    <div class="color-images-container">
                        <input type="file"
                        class="form-control color-images-input"
                        name="variants[${rowIndex}][color_images][]"
                        multiple accept="image/*">
                        <div class="preview-container d-flex flex-wrap mt-2 gap-2"></div>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
                </td>
            `;
            table.appendChild(tr);
            rowIndex++;
        });

        table.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-variant')) {
                e.target.closest('tr').remove();
            }
        });
    })();

    // Filter categories by main category
    const allCategories = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'main_category_id' => $c->main_category_id]));
    function filterCategories(mainSelectId, categorySelectId) {
        const mainSelect = document.getElementById(mainSelectId);
        const categorySelect = document.getElementById(categorySelectId);
        mainSelect.addEventListener('change', function() {
            const selectedMain = this.value;
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            allCategories.forEach(function(cat) {
                if (cat.main_category_id == selectedMain) {
                    categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                }
            });
        });
    }
    filterCategories('main_category_id', 'category_id');
    filterCategories('main_category_id_2', 'category_id_2');
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const select = document.getElementById("origin_country_select");
    const hiddenInput = document.getElementById("origin_country");

    // Fetch country list with required fields
    fetch("https://restcountries.com/v3.1/all?fields=name")
        .then(response => response.json())
        .then(data => {
            // Sort countries alphabetically
            const countries = data.sort((a, b) =>
                a.name.common.localeCompare(b.name.common)
            );

            // Add options to dropdown
            countries.forEach(country => {
                const option = document.createElement("option");
                option.value = country.name.common;
                option.textContent = country.name.common;
                select.appendChild(option);
            });

            // If there's an old value, select it
            if (hiddenInput.value) {
                select.value = hiddenInput.value;
            }
        })
        .catch(err => console.error("Error fetching countries:", err));

    // When user selects a country, update hidden input
    select.addEventListener("change", function() {
        hiddenInput.value = this.value;
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const priceInput = document.getElementById("price");
    const discountInput = document.getElementById("discount_percentage");
    const finalPriceInput = document.getElementById("final_price");

    function updateFinalPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        // Make sure discount stays between 0–100
        const validDiscount = Math.min(Math.max(discount, 0), 100);

        // Calculate discounted price
        const discountedPrice = price - (price * validDiscount / 100);

        finalPriceInput.value = discountedPrice.toFixed(2);
    }

    // Listen for price or discount changes
    priceInput.addEventListener("input", updateFinalPrice);
    discountInput.addEventListener("input", updateFinalPrice);
});

// Deposit functionality
document.addEventListener("DOMContentLoaded", function() {
    const hasDepositCheckbox = document.getElementById('has_deposit');
    const depositContainer = document.getElementById('deposit_details_container');
    const priceInput = document.getElementById('price');
    const depositAmountInput = document.getElementById('deposit_amount');
    const depositPercentageInput = document.getElementById('deposit_percentage');
    const finalPriceDisplay = document.getElementById('final_price_after_deposit_display');
    const finalPriceHidden = document.getElementById('final_price_after_deposit');

    // Toggle deposit details container
    if (hasDepositCheckbox) {
        hasDepositCheckbox.addEventListener('change', function() {
            if (this.checked) {
                depositContainer.style.display = 'block';
                calculateFinalPriceAfterDeposit();
            } else {
                depositContainer.style.display = 'none';
                finalPriceDisplay.value = '';
                finalPriceHidden.value = '';
            }
        });
    }

    // Calculate final price after deposit
    function calculateFinalPriceAfterDeposit() {
        if (!hasDepositCheckbox || !hasDepositCheckbox.checked) {
            return;
        }

        const price = parseFloat(priceInput.value) || 0;
        let deposit = 0;

        // Calculate deposit based on amount or percentage
        const depositAmount = parseFloat(depositAmountInput.value) || 0;
        const depositPercentage = parseFloat(depositPercentageInput.value) || 0;

        if (depositAmount > 0) {
            deposit = depositAmount;
        } else if (depositPercentage > 0) {
            deposit = price * (depositPercentage / 100);
        }

        // Ensure deposit doesn't exceed price
        deposit = Math.min(deposit, price);

        const finalPrice = price - deposit;

        finalPriceDisplay.value = finalPrice.toFixed(2);
        finalPriceHidden.value = finalPrice.toFixed(2);
    }

    // Listen for changes in price and deposit inputs
    if (priceInput) priceInput.addEventListener('input', calculateFinalPriceAfterDeposit);
    if (depositAmountInput) depositAmountInput.addEventListener('input', calculateFinalPriceAfterDeposit);
    if (depositPercentageInput) depositPercentageInput.addEventListener('input', calculateFinalPriceAfterDeposit);

    // Initialize on page load
    calculateFinalPriceAfterDeposit();
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const freeShippingSelect = document.getElementById("free_shipping");
    const freeShippingPriceContainer = document.getElementById("free_shipping_price_container");

    freeShippingSelect.addEventListener("change", function() {
        if (this.value === "price") {
            freeShippingPriceContainer.style.display = "block";
        } else {
            freeShippingPriceContainer.style.display = "none";
        }
    });
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        placeholder: "Select delivery zones",
        allowClear: true,
        width: '100%'
    });
});
</script>

<script>
$(document).ready(function () {
    $('#delivery_options').select2({
        placeholder: "Select delivery options",
        allowClear: true,
        width: '100%'
    });
});
</script>

<script>
function initColorImagePreview(container) {
    const input = container.querySelector('.color-images-input');
    const preview = container.querySelector('.preview-container');
    let filesArray = [];

    input.addEventListener('change', function(e) {
        filesArray = [...filesArray, ...e.target.files];
        renderPreview();
    });

    function renderPreview() {
        preview.innerHTML = '';
        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';

                wrapper.innerHTML = `
                    <img src="${ev.target.result}"
                         class="img-thumbnail w-100 h-100 object-fit-cover rounded"
                         alt="Preview">
                    <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 px-1">&times;</button>
                `;

                wrapper.querySelector('button').addEventListener('click', () => {
                    filesArray.splice(index, 1);
                    renderPreview();
                });

                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        const dataTransfer = new DataTransfer();
        filesArray.forEach(f => dataTransfer.items.add(f));
        input.files = dataTransfer.files;
    }
}

// Initialize existing color input on load
document.querySelectorAll('.color-images-container').forEach(initColorImagePreview);

// Make sure new rows also get initialized
document.getElementById('add-variant').addEventListener('click', function() {
    setTimeout(() => {
        document.querySelectorAll('.color-images-container').forEach(container => {
            if (!container.dataset.initialized) {
                initColorImagePreview(container);
                container.dataset.initialized = true;
            }
        });
    }, 100);
});
</script>

<script>
    function handleFilePreview(inputId, previewId, type = 'image') {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        let filesArray = [];

        input.addEventListener('change', function(e) {
            filesArray = [...filesArray, ...Array.from(e.target.files)];
            renderPreview();
        });

        function renderPreview() {
            preview.innerHTML = '';

            filesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const div = document.createElement('div');
                    div.className = 'col-6 mb-3 position-relative';

                    let content = '';
                    if (type === 'image') {
                        content = `<img src="${ev.target.result}" class="img-thumbnail" alt="Preview" style="width: 100px; height: 100px; object-fit: cover;">`;
                    } else {
                        content = `
                            <video controls width="100%" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                <source src="${ev.target.result}" type="${file.type}">
                                Your browser does not support the video tag.
                            </video>`;
                    }

                    div.innerHTML = `
                        ${content}
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-preview" data-index="${index}">&times;</button>
                    `;

                    div.querySelector('.remove-preview').addEventListener('click', function() {
                        const removeIndex = parseInt(this.getAttribute('data-index'));
                        filesArray.splice(removeIndex, 1);
                        renderPreview();
                        updateFileInput();
                    });

                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });

            updateFileInput();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            filesArray.forEach(f => dataTransfer.items.add(f));
            input.files = dataTransfer.files;
        }
    }

    // Preview with remove option
    handleFilePreview('images', 'image-preview', 'image');
    handleFilePreview('extra_images', 'extra-image-preview', 'image');
    handleFilePreview('videos', 'video-preview', 'video');

    // Enhanced variant management with proper image handling
(function() {
    const table = document.getElementById('variants-table').getElementsByTagName('tbody')[0];
    const addBtn = document.getElementById('add-variant');
    let rowIndex = 1; // Start from 1 since we already have one row
    const colors = @json($colors->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
    const sizes = @json($sizes->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));

    function createVariantRow(index) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="variants[${index}][color_id]" class="form-select">
                    <option value="">None</option>
                    ${colors.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="variants[${index}][size_id]" class="form-select">
                    <option value="">None</option>
                    ${sizes.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${index}][price]" class="form-control" step="0.01" min="0" placeholder="Auto = main price">
            </td>
            <td>
                <input type="number" name="variants[${index}][stock]" class="form-control" step="1" min="0" value="0">
            </td>
            <td>
                <div class="color-images-container">
                    <input type="file"
                        class="form-control color-images-input"
                        name="variants[${index}][color_images][]"
                        multiple accept="image/*">
                    <div class="preview-container d-flex flex-wrap mt-2 gap-2"></div>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
            </td>
        `;
        return tr;
    }

    addBtn.addEventListener('click', function() {
        const tr = createVariantRow(rowIndex);
        table.appendChild(tr);

        // Initialize color image preview for new row immediately
        const newContainer = tr.querySelector('.color-images-container');
        initColorImagePreview(newContainer);

        rowIndex++;

        // Enable remove buttons if there's more than one row
        const removeButtons = table.querySelectorAll('.remove-variant');
        if (removeButtons.length > 1) {
            removeButtons.forEach(btn => btn.disabled = false);
        }
    });

    table.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-variant')) {
            const row = e.target.closest('tr');
            const removeButtons = table.querySelectorAll('.remove-variant');

            if (removeButtons.length > 1) {
                row.remove();

                const remainingButtons = table.querySelectorAll('.remove-variant');
                if (remainingButtons.length === 1) {
                    remainingButtons[0].disabled = true;
                }
            }
        }
    });

    // Initialize all existing color image inputs on page load
    document.querySelectorAll('.color-images-container').forEach(container => {
        initColorImagePreview(container);
    });
})();

    // Filter categories by main category
    const allCategories = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'main_category_id' => $c->main_category_id]));
    function filterCategories(mainSelectId, categorySelectId) {
        const mainSelect = document.getElementById(mainSelectId);
        const categorySelect = document.getElementById(categorySelectId);

        // Set selected values for category selects
        categorySelect.dataset.selected = "{{ old('category_id') }}";

        // Initial filtering based on current selection
        const selectedMain = mainSelect.value;
        if (selectedMain) {
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            allCategories.forEach(function(cat) {
                if (cat.main_category_id == selectedMain) {
                    const isSelected = categorySelect.dataset.selected == cat.id;
                    categorySelect.innerHTML += `<option value="${cat.id}" ${isSelected ? 'selected' : ''}>${cat.name}</option>`;
                }
            });
        }

        mainSelect.addEventListener('change', function() {
            const selectedMain = this.value;
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            allCategories.forEach(function(cat) {
                if (cat.main_category_id == selectedMain) {
                    categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                }
            });
        });
    }

    // Initialize category filtering
    filterCategories('main_category_id', 'category_id');
    filterCategories('main_category_id_2', 'category_id_2');
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const select = document.getElementById("origin_country_select");
    const hiddenInput = document.getElementById("origin_country");

    // Fetch country list with required fields
    fetch("https://restcountries.com/v3.1/all?fields=name")
        .then(response => response.json())
        .then(data => {
            // Sort countries alphabetically
            const countries = data.sort((a, b) =>
                a.name.common.localeCompare(b.name.common)
            );

            // Add options to dropdown
            countries.forEach(country => {
                const option = document.createElement("option");
                option.value = country.name.common;
                option.textContent = country.name.common;
                select.appendChild(option);
            });

            // If there's an old value, select it
            if (hiddenInput.value) {
                select.value = hiddenInput.value;
            }
        })
        .catch(err => console.error("Error fetching countries:", err));

    // When user selects a country, update hidden input
    select.addEventListener("change", function() {
        hiddenInput.value = this.value;
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const priceInput = document.getElementById("price");
    const discountInput = document.getElementById("discount_percentage");
    const finalPriceInput = document.getElementById("final_price");

    function updateFinalPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        // Make sure discount stays between 0–100
        const validDiscount = Math.min(Math.max(discount, 0), 100);

        // Calculate discounted price
        const discountedPrice = price - (price * validDiscount / 100);

        finalPriceInput.value = discountedPrice.toFixed(2);
    }

    // Listen for price or discount changes
    priceInput.addEventListener("input", updateFinalPrice);
    discountInput.addEventListener("input", updateFinalPrice);

    // Initialize on page load
    updateFinalPrice();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const freeShippingSelect = document.getElementById("free_shipping");
    const freeShippingPriceContainer = document.getElementById("free_shipping_price_container");

    freeShippingSelect.addEventListener("change", function() {
        if (this.value === "price") {
            freeShippingPriceContainer.style.display = "block";
        } else {
            freeShippingPriceContainer.style.display = "none";
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const existingMedia = document.getElementById('existing-media');
    const removedMediaContainer = document.getElementById('removed-media-container');

    if (existingMedia) {
        existingMedia.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('js-remove-media')) {
                const id = e.target.getAttribute('data-id');
                const col = e.target.closest('[data-media-id]');

                if (id && col) {
                    // Create hidden input to track removed media
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_media_ids[]';
                    input.value = id;
                    removedMediaContainer.appendChild(input);

                    // Remove the media element from UI
                    col.remove();

                    // Show message if no media left
                    if (existingMedia.children.length === 0) {
                        existingMedia.innerHTML = '<p class="text-muted">No media remaining.</p>';
                    }
                }
            }
        });
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#delivery_zones').select2({
        placeholder: "Select delivery zones",
        allowClear: true,
        width: '100%'
    });

    $('#delivery_options').select2({
        placeholder: "Select delivery options",
        allowClear: true,
        width: '100%'
    });
});
</script>

<script>
function initColorImagePreview(container) {
    const input = container.querySelector('.color-images-input');
    const preview = container.querySelector('.preview-container');
    let filesArray = [];

    // Clear any existing event listeners by cloning the input
    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);
    const actualInput = container.querySelector('.color-images-input');

    actualInput.addEventListener('change', function(e) {
        const newFiles = Array.from(e.target.files);
        filesArray = [...filesArray, ...newFiles];
        renderPreview();
    });

    function renderPreview() {
        preview.innerHTML = '';

        filesArray.forEach((file, index) => {
            // Only process image files
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';

                wrapper.innerHTML = `
                    <img src="${ev.target.result}"
                         class="img-thumbnail w-100 h-100 object-fit-cover rounded"
                         alt="Preview" style="object-fit: cover;">
                    <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 px-1 remove-preview"
                            data-index="${index}">&times;</button>
                `;

                wrapper.querySelector('.remove-preview').addEventListener('click', function() {
                    const removeIndex = parseInt(this.getAttribute('data-index'));
                    filesArray.splice(removeIndex, 1);
                    renderPreview();
                    updateFileInput();
                });

                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        updateFileInput();
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach(f => dataTransfer.items.add(f));
        actualInput.files = dataTransfer.files;
    }

    // Initial render
    renderPreview();
}

// Initialize existing color input on load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.color-images-container').forEach(container => {
        initColorImagePreview(container);
    });
});
</script>

<script>
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('js-remove-media')) {
        const btn = e.target;
        const id = btn.dataset.id;
        const previewItem = btn.closest('[data-media-id]');

        if (!id || !previewItem) return;

        // Ensure we have a container to store removed IDs
        let removedContainer = document.getElementById('removed-media-container');
        if (!removedContainer) {
            removedContainer = document.createElement('div');
            removedContainer.id = 'removed-media-container';
            document.querySelector('form').appendChild(removedContainer);
        }

        // Add hidden input to form
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_media_ids[]';
        input.value = id;
        removedContainer.appendChild(input);

        // Remove preview visually
        previewItem.remove();

        // Show message if no media left in the container
        const mediaContainer = previewItem.closest('.row') || previewItem.closest('.preview-container');
        if (mediaContainer && mediaContainer.children.length === 0) {
            const emptyMessage = document.createElement('p');
            emptyMessage.className = 'text-muted';
            emptyMessage.textContent = 'No media remaining.';
            mediaContainer.appendChild(emptyMessage);
        }
    }
});
</script>

<script>
    // Return policy toggle functionality - Updated version
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('is_returnable');
        const container = document.getElementById('return_details_container');

        if (!checkbox || !container) {
            console.error('Return policy elements not found');
            return;
        }

        // Function to toggle the container visibility
        function toggleReturnDetails() {
            if (checkbox.checked) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                // Optional: Clear values when hiding
                const returnValidityInput = document.getElementById('return_validity');
                const returnFeeInput = document.getElementById('return_fee');
                if (returnValidityInput) returnValidityInput.value = '';
                if (returnFeeInput) returnFeeInput.value = '';
            }
        }

        // Add event listener for checkbox changes
        checkbox.addEventListener('change', toggleReturnDetails);

        // Initialize the state on page load
        toggleReturnDetails();
    });
</script>

<script>
    // Variants checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const hasVariantsCheckbox = document.getElementById('has_variants');
        const variantsSection = document.getElementById('variants-section');
        const variantsTable = document.getElementById('variants-table');
        const stockInputGroup = document.getElementById('stock-input-group');
        const addVariantBtn = document.getElementById('add-variant');

        if (!hasVariantsCheckbox || !variantsSection) {
            return;
        }

        function toggleVariantsSection() {
            if (hasVariantsCheckbox.checked) {
                variantsSection.style.display = 'block';
                // Show the first row in the table
                const firstRow = variantsTable.querySelector('tbody tr');
                if (firstRow) {
                    if (firstRow.style.display === 'none') {
                        firstRow.style.display = '';
                        // Initialize color image preview for the first row
                        const container = firstRow.querySelector('.color-images-container');
                        if (container && typeof initColorImagePreview === 'function') {
                            initColorImagePreview(container);
                        }
                    }
                } else {
                    // If no row exists, trigger add variant button
                    if (addVariantBtn) {
                        addVariantBtn.click();
                    }
                }
                // Hide stock input when using variants
                if (stockInputGroup) {
                    stockInputGroup.style.display = 'none';
                }
            } else {
                variantsSection.style.display = 'none';
                // Hide all variant rows
                const rows = variantsTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    row.style.display = 'none';
                });
                // Show stock input when not using variants
                if (stockInputGroup) {
                    stockInputGroup.style.display = 'block';
                }
            }
        }

        // Add event listener
        hasVariantsCheckbox.addEventListener('change', toggleVariantsSection);

        // Initialize on page load
        toggleVariantsSection();

        // Remove variants from form submission if checkbox is not checked
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!hasVariantsCheckbox.checked) {
                    // Remove all variant inputs completely
                    const variantInputs = form.querySelectorAll('[name^="variants["]');
                    variantInputs.forEach(input => {
                        input.remove();
                    });
                } else {
                    // If checkbox is checked, make sure all variant inputs are enabled
                    const variantInputs = form.querySelectorAll('[name^="variants["]');
                    variantInputs.forEach(input => {
                        input.disabled = false;
                    });
                }
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const discountStartInput = document.getElementById('discount_start');
    const discountEndInput = document.getElementById('discount_end');

    if (!discountStartInput || !discountEndInput) {
        return;
    }

    function getTodayString() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const todayStr = getTodayString();
    discountStartInput.min = todayStr;
    discountEndInput.min = todayStr;

    function validateDiscountDates() {
        const startVal = discountStartInput.value;
        const endVal = discountEndInput.value;

        discountStartInput.setCustomValidity('');
        discountEndInput.setCustomValidity('');

        if (startVal && startVal < todayStr) {
            discountStartInput.setCustomValidity("{{ __('vendor-dashboard.discount_start_must_be_today_or_later') }}");
        }

        if (endVal && endVal < todayStr) {
            discountEndInput.setCustomValidity("{{ __('vendor-dashboard.discount_end_must_be_today_or_later') }}");
        }

        if (startVal && endVal && endVal < startVal) {
            discountEndInput.setCustomValidity("{{ __('vendor-dashboard.discount_end_must_be_after_start') }}");
        }

        // Trigger immediate browser validation UI if any field is invalid
        if (!discountStartInput.checkValidity()) {
            discountStartInput.reportValidity();
        } else if (!discountEndInput.checkValidity()) {
            discountEndInput.reportValidity();
        }
    }

    discountStartInput.addEventListener('change', validateDiscountDates);
    discountEndInput.addEventListener('change', validateDiscountDates);
});
</script>
@endsection
