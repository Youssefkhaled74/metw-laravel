@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_size'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.edit_size') }}</h1>
        <a href="{{ route('admin.settings.sizes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.sizes.update', $size) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="title" class="form-label required">{{ __('admin-dashboard.size_title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $size->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-4">
                    <!-- Update Icon Section -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-upload me-1"></i> {{ __('admin-dashboard.upload_icon') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="icon" class="fw-bold">{{ __('admin-dashboard.choose_icon') }}</label>
                                    <input type="file" class="form-control @error('icon') is-invalid @enderror"
                                           id="icon" name="icon" accept="image/*">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('admin-dashboard.icon_help') }}
                                    </small>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="icon-preview" class="text-center mt-3">
                                    <!-- New icon preview will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Icon Section -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-image me-1"></i> Current Icon</h6>
                            </div>
                            <div class="card-body text-center">
                                @if($size->icon)
                                    <img src="{{ asset($size->icon) }}" alt="Current Icon"
                                         class="img-thumbnail shadow-sm" style="max-width: 200px;">
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>No icon currently set</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $size->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Size</button>
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
        const previewContainer = document.getElementById('icon-preview');

        // Clear existing preview
        previewContainer.innerHTML = '';

        if (file) {
            // Show loading indicator
            previewContainer.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview container
                const previewWrapper = document.createElement('div');
                previewWrapper.classList.add('border', 'rounded', 'p-2');

                // Create and setup image preview
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.style.maxWidth = '200px';
                preview.classList.add('img-thumbnail');

                // Create preview label
                const label = document.createElement('div');
                label.classList.add('mt-2', 'text-muted', 'small');
                label.innerHTML = '<i class="fas fa-check-circle text-success"></i> New icon preview';

                // Add elements to preview container
                previewWrapper.appendChild(preview);
                previewWrapper.appendChild(label);

                // Clear loading indicator and show preview
                previewContainer.innerHTML = '';
                previewContainer.appendChild(previewWrapper);
            }

            reader.onerror = function() {
                previewContainer.innerHTML = '<div class="alert alert-danger">Error loading preview</div>';
            }

            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
