@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_delivery_type'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.edit_delivery_type') }}</h1>
        <a href="{{ route('admin.settings.delivery-types.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.delivery-types.update', $deliveryType) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="name">{{ __('admin-dashboard.delivery_type_name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $deliveryType->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">{{ __('admin-dashboard.delivery_type_description') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description', $deliveryType->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                           id="price" name="price" value="{{ old('price', $deliveryType->price) }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_time">Delivery Time</label>
                    <input type="text" class="form-control @error('delivery_time') is-invalid @enderror"
                           id="delivery_time" name="delivery_time" value="{{ old('delivery_time', $deliveryType->delivery_time) }}" required>
                    <small class="form-text text-muted">Example: "1-2 business days", "24 hours"</small>
                    @error('delivery_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $deliveryType->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update_delivery_type') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
