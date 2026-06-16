@extends('layouts.admin')

@section('title', __('admin-dashboard.create_new_category'))
@section('page-title', __('admin-dashboard.create_new_category'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.categories.index') }}">{{ __('admin-dashboard.categories') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_new_category') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.category_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.categories.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="main_category_id" class="form-label required">{{ __('admin-dashboard.main_category') }} <span class="text-danger">*</span>
</label>
                                <select class="form-select @error('main_category_id') is-invalid @enderror"
                                        id="main_category_id"
                                        name="main_category_id"
                                        required>
                                    <option value="">{{ __('admin-dashboard.select_main_category') }}</option>

                                    @foreach ($mainCategories as $mainCategory)
                                        <option value="{{ $mainCategory->id }}"
                                            {{ old('main_category_id') == $mainCategory->id ? 'selected' : '' }}>
                                            {{ $mainCategory->translation()?->name ?? $mainCategory->name }}
                                        </option>
                                    @endforeach
                                </select>

                            @error('main_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_en" class="form-label required">{{ __('admin-dashboard.category_name_en') }}</label>
                            <input type="text"
                                class="form-control @error('translations.en.name') is-invalid @enderror"
                                id="name_en"
                                name="translations[en][name]"
                                value="{{ old('translations.en.name') }}" required>
                            @error('translations.en.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_ar" class="form-label required">{{ __('admin-dashboard.category_name_ar') }}</label>
                            <input type="text"
                                class="form-control @error('translations.ar.name') is-invalid @enderror"
                                id="name_ar"
                                name="translations[ar][name]"
                                value="{{ old('translations.ar.name') }}" required>
                            @error('translations.ar.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">
                                {{ __('admin-dashboard.category_type') }}
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select @error('type') is-invalid @enderror"
                                name="type"
                                id="type"
                                required
                            >
                                <option value="">{{ __('admin-dashboard.select_type') }}</option>
                                <option value="piece" {{ old('type') == 'piece' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_piece') }}
                                </option>
                                <option value="weight" {{ old('type') == 'weight' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_weight') }}
                                </option>
                                <option value="weight_size" {{ old('type') == 'weight_size' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_weight_size') }}
                                </option>
                            </select>

                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="image" class="form-label required">{{ __('admin-dashboard.category_image') }}</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept="image/*" required>
                            <div class="form-text">{{ __('admin-dashboard.category_image_size') }}</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.create_category_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.category_image_preview') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center" id="imagePreview">
                        <img src="{{ asset('images/placeholder.png') }}" alt="Preview" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview uploaded image
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('#imagePreview img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Auto-generate slug preview
        document.getElementById('name').addEventListener('input', function(e) {
            const name = e.target.value;
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();

            // You could add a slug preview here if needed
        });
    </script>
@endpush

@push('styles')
    <style>
        .required:after {
            content: " *";
            color: red;
        }
    </style>
@endpush
