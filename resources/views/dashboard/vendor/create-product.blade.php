@extends('layouts.vendor')

@section('title', __('vendor-dashboard.create_product'))
@section('page-title', __('vendor-dashboard.create_new_product'))

@section('page-actions')
    <a href="{{ route('vendor.products') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('vendor-dashboard.back_to_products') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('vendor-dashboard.product_information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('vendor-dashboard.product_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">{{ __('vendor-dashboard.category') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">{{ __('vendor-dashboard.select_category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('vendor-dashboard.price') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01"
                                            class="form-control @error('price') is-invalid @enderror" id="price"
                                            name="price" value="{{ old('price') }}" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">{{ __('vendor-dashboard.short_description') }} <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                                name="short_description" rows="3" required>{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('vendor-dashboard.full_description') }} <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">{{ __('vendor-dashboard.product_images') }} <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                                id="images" name="images[]" multiple accept="image/*" required>
                            <div class="form-text">{{ __('vendor-dashboard.select_multiple_images') }}</div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sizes" class="form-label">{{ __('vendor-dashboard.available_sizes') }}</label>
                                    <select class="form-select" id="sizes" name="sizes[]" multiple>
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}"
                                                {{ in_array($size->id, old('sizes', [])) ? 'selected' : '' }}>
                                                {{ $size->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('vendor-dashboard.hold_ctrl_select') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="colors" class="form-label">{{ __('vendor-dashboard.available_colors') }}</label>
                                    <select class="form-select" id="colors" name="colors[]" multiple>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ in_array($color->id, old('colors', [])) ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">{{ __('vendor-dashboard.hold_ctrl_select_colors') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('vendor.products') }}" class="btn btn-secondary me-md-2">{{ __('vendor-dashboard.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('vendor-dashboard.create_product') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
