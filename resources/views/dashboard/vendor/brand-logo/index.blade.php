@extends('layouts.vendor')

@section('title', __('vendor-dashboard.brand_logo'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">@lang('vendor-dashboard.brand_logo')</h4>
        <p class="text-muted mb-0 small">@lang('vendor-dashboard.brand_logo_subtitle')</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">@lang('vendor-dashboard.current_logo')</h6>
            </div>
            <div class="card-body text-center">
                @if($vendor->logo)
                    <div class="logo-preview-wrapper mb-3">
                        <img src="{{ asset('storage/' . $vendor->logo) }}"
                             alt="{{ $vendor->brand_name ?? $vendor->name }}"
                             class="img-fluid rounded logo-preview">
                    </div>
                    @if($vendor->brand_name)
                        <p class="fw-semibold mb-2">{{ $vendor->brand_name }}</p>
                    @endif
                    <form action="{{ route('vendor.brand-logo.destroy') }}" method="POST"
                          onsubmit="return confirm('@lang('vendor-dashboard.confirm_remove_logo')')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-1"></i> @lang('vendor-dashboard.remove_logo')
                        </button>
                    </form>
                @else
                    <div class="logo-placeholder mb-3">
                        <i class="fas fa-image"></i>
                    </div>
                    <p class="text-muted">@lang('vendor-dashboard.no_logo_uploaded')</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">@lang('vendor-dashboard.upload_logo')</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.brand-logo.update') }}" method="POST" enctype="multipart/form-data" id="logoForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="brand_name" class="form-label fw-semibold">@lang('vendor-dashboard.brand_name')</label>
                        <input type="text"
                               class="form-control @error('brand_name') is-invalid @enderror"
                               id="brand_name"
                               name="brand_name"
                               value="{{ old('brand_name', $vendor->brand_name) }}"
                               placeholder="@lang('vendor-dashboard.brand_name_placeholder')">
                        @error('brand_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label fw-semibold">@lang('vendor-dashboard.logo_file')</label>
                        <input type="file"
                               class="form-control @error('logo') is-invalid @enderror"
                               id="logo"
                               name="logo"
                               accept="image/jpeg,image/png,image/gif,image/svg+xml"
                               onchange="previewLogo(this)">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">JPEG, PNG, GIF, SVG — @lang('vendor-dashboard.max_size_2mb')</small>
                    </div>

                    <div id="logoPreviewContainer" class="mb-3" style="display: none;">
                        <label class="form-label fw-semibold">@lang('vendor-dashboard.preview')</label>
                        <div class="text-center">
                            <img id="logoPreview" class="img-fluid rounded logo-preview" alt="Preview">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> @lang('vendor-dashboard.save_logo')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .logo-preview {
        max-height: 200px;
        object-fit: contain;
    }
    .logo-preview-wrapper {
        background: #f8f9fa;
        border-radius: 14px;
        padding: 1.5rem;
        display: inline-block;
    }
    .logo-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 14px;
        background: #f0f4ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #4f46e5;
    }
</style>

<script>
function previewLogo(input) {
    const container = document.getElementById('logoPreviewContainer');
    const preview = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.style.display = 'none';
    }
}
</script>
@endsection
