@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_banner'))
@section('page-title', __('admin-dashboard.edit_banner'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.banners.index') }}">{{ __('admin-dashboard.banners') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.edit_banner') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0 d-flex align-items-center">
                    <div class="me-2 banner-form-icon bg-warning text-white">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-semibold">{{ __('admin-dashboard.edit_banner_details') }}</h5>
                        <small class="text-muted">{{ __('admin-dashboard.recommended_size') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.banners.update', $banner->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="image" class="form-label small text-muted text-uppercase fw-semibold">
                                {{ __('admin-dashboard.banner_image') }}
                            </label>
                            <input type="file"
                                   class="form-control form-control-sm @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <div class="form-text">
                                {{ __('admin-dashboard.keep_current_image') }} {{ __('admin-dashboard.recommended_size') }}
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3">
                            <label for="link" class="form-label">{{ __('admin-dashboard.link_url') }}</label>
                            <input type="url"
                                   class="form-control @error('link') is-invalid @enderror"
                                   id="link"
                                   name="link"
                                   value="{{ old('link', $banner->link) }}"
                                   placeholder="{{ __('admin-dashboard.link_url_placeholder') }}">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.settings.banners.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('admin-dashboard.back_to_list') }}
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i> {{ __('admin-dashboard.update_banner') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0">
                    <h6 class="mb-0 fw-semibold">
                        <i class="far fa-image me-1 text-primary"></i>{{ __('admin-dashboard.current_image') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center" id="imagePreview">
                        <div class="banner-preview-wrapper mx-auto">
                            <img src="{{ asset($banner->image) }}"
                                 alt="{{ __('admin-dashboard.current_image') }}"
                                 class="img-fluid rounded">
                        </div>
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
    .banner-form-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .banner-preview-wrapper {
        max-width: 100%;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }

    .banner-preview-wrapper img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
</style>
@endpush
