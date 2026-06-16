@extends('admin.layouts.app')

@section('title', __('admin-dashboard.edit_category'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.edit_category') }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-dashboard.edit_category_details') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Parent Category -->
                    <div class="col-md-12 mb-4">
                        <div class="form-group">
                            <label for="parent_id">{{ __('admin-dashboard.main_category') }}</label>
                            <select name="parent_id" id="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                <option value="">{{ __('admin-dashboard.select_main_category') }}</option>
                                @foreach($categories as $parentCategory)
                                    @if($parentCategory->id !== $category->id)
                                        <option value="{{ $parentCategory->id }}"
                                            {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                            {{ $parentCategory->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Name (English) -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="name_en">
                                {{ __('admin-dashboard.category_name_en') }}
                                <span class="text-danger">{{ __('admin-dashboard.required_field') }}</span>
                            </label>
                            <input type="text" name="name_en" id="name_en"
                                   class="form-control @error('name_en') is-invalid @enderror"
                                   value="{{ old('name_en', $category->translate('en')->name) }}" required>
                            @error('name_en')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Name (Arabic) -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="name_ar">
                                {{ __('admin-dashboard.category_name_ar') }}
                                <span class="text-danger">{{ __('admin-dashboard.required_field') }}</span>
                            </label>
                            <input type="text" name="name_ar" id="name_ar"
                                   class="form-control @error('name_ar') is-invalid @enderror"
                                   value="{{ old('name_ar', $category->translate('ar')->name) }}" required>
                            @error('name_ar')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Slug -->
                    <div class="col-md-12 mb-4">
                        <div class="form-group">
                            <label>{{ __('admin-dashboard.current_slug') }}</label>
                            <p class="form-control-static">{{ $category->slug }}</p>
                            <small class="text-muted">{{ __('admin-dashboard.slug_auto_update') }}</small>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="col-md-12 mb-4">
                        <div class="form-group">
                            <label for="image">{{ __('admin-dashboard.category_image') }}</label>

                            @if($category->image)
                                <div class="mb-3">
                                    <label>{{ __('admin-dashboard.current_image') }}:</label><br>
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                         class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @endif

                            <div class="custom-file">
                                <input type="file" name="image" id="image"
                                       class="custom-file-input @error('image') is-invalid @enderror"
                                       accept="image/*">
                                <label class="custom-file-label" for="image">{{ __('admin-dashboard.choose_file') }}</label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('admin-dashboard.category_image_size') }}<br>
                                {{ __('admin-dashboard.keep_current_image') }}
                            </small>
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <label>{{ __('admin-dashboard.preview') }}:</label><br>
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="col-md-12 mb-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input"
                                       id="isActive" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="isActive">{{ __('admin-dashboard.active') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('admin-dashboard.update_category') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Image preview
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
                $(this).next('.custom-file-label').html(file.name);
            }
        });
    });
</script>
@endpush
