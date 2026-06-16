@extends('layouts.admin')

@section('title', __('admin-dashboard.add_size'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.add_size') }}</h1>
        <a href="{{ route('admin.settings.sizes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.sizes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="title" class="form-label required">{{ __('admin-dashboard.size_title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="icon" class="form-label required">{{ __('admin-dashboard.size_icon') }}</label>
                    <input type="file" class="form-control-file @error('icon') is-invalid @enderror"
                           id="icon" name="icon" accept="image/*" required>
                    <small class="form-text text-muted">{{ __('admin-dashboard.icon_help') }}</small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.create_size') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview uploaded image
    document.getElementById('icon').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.style.maxWidth = '200px';
                preview.classList.add('mt-2');
                const container = document.getElementById('icon').parentElement;
                const existingPreview = container.querySelector('img');
                if (existingPreview) {
                    container.removeChild(existingPreview);
                }
                container.appendChild(preview);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
