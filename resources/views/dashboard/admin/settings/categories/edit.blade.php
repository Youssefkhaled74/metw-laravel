@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_category'))
@section('page-title', __('admin-dashboard.edit_category'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.categories.index') }}">{{ __('admin-dashboard.categories') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.edit_category') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.edit_category_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.categories.update', $category->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="main_category_id" class="form-label required">{{ __('admin-dashboard.main_category') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('main_category_id') is-invalid @enderror"
                                id="main_category_id" name="main_category_id" required>
                                <option value="">{{ __('admin-dashboard.select_main_category') }}</option>
                                @foreach ($mainCategories as $mainCategory)
                                    <option value="{{ $mainCategory->id }}"
                                        {{ old('main_category_id', $category->main_category_id) == $mainCategory->id ? 'selected' : '' }}>
                                        {{ $mainCategory->name }}</option>
                                @endforeach
                            </select>
                            @error('main_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label required">Category Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label required">
                                {{ __('admin-dashboard.category_type') }}<span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select @error('type') is-invalid @enderror"
                                id="type"
                                name="type"
                                required
                            >
                                <option value="">{{ __('admin-dashboard.select_type') }}</option>

                                <option value="piece"
                                    {{ old('type', $category->type) == 'piece' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_piece') }}
                                </option>

                                <option value="weight"
                                    {{ old('type', $category->type) == 'weight' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_weight') }}
                                </option>

                                <option value="weight_size"
                                    {{ old('type', $category->type) == 'weight_size' ? 'selected' : '' }}>
                                    {{ __('admin-dashboard.type_weight_size') }}
                                </option>
                            </select>

                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="image" class="form-label">Category Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept="image/*">
                            <div class="form-text">Leave empty to keep the current image. Upload a square image for best
                                results. Max size: 2MB</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Slug</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-link"></i>
                                </span>
                                <input type="text" class="form-control" value="{{ $category->slug }}" readonly>
                            </div>
                            <div class="form-text">The slug will be automatically updated based on the category name.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Current Image</h5>
                </div>
                <div class="card-body">
                    <div class="text-center" id="imagePreview">
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded">
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
    </script>
@endpush
