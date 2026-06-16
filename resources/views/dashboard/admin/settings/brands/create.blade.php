@extends('layouts.admin')

@section('title', __('admin-dashboard.create_brand'))
@section('page-title', __('admin-dashboard.create_brand'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.brands.index') }}">{{ __('admin-dashboard.brands') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_brand') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.brand_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.brands.store') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name_en" class="form-label required">{{ __('admin-dashboard.brand_name_en') }}{{ __('admin-dashboard.required_field') }}</label>
                            <input type="text"
                                   class="form-control @error('name_en') is-invalid @enderror"
                                   id="name_en"
                                   name="name_en"
                                   value="{{ old('name_en') }}"
                                   required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_ar" class="form-label required">{{ __('admin-dashboard.brand_name_ar') }}{{ __('admin-dashboard.required_field') }}</label>
                            <input type="text"
                                   class="form-control @error('name_ar') is-invalid @enderror"
                                   id="name_ar"
                                   name="name_ar"
                                   value="{{ old('name_ar') }}"
                                   required>
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label required">{{ __('admin-dashboard.brand_image') }}{{ __('admin-dashboard.required_field') }}</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*"
                                   required>
                            <div class="form-text">{{ __('admin-dashboard.brand_logo_size') }}</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.brands.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.create_brand_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.preview') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center" id="imagePreview">
                        <img src="{{ asset('images/placeholder.png') }}"
                             alt="{{ __('admin-dashboard.preview') }}"
                             class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
