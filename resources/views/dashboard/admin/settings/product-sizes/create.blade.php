@extends('layouts.admin')

@section('title', __('admin-dashboard.create_product_size'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.create_product_size') }}</h1>
        <a href="{{ route('admin.settings.product-sizes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.product-sizes.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="title">{{ __('admin-dashboard.product_size_title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
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

                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.create_product_size') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
