@extends('layouts.admin')

@section('title', __('admin-dashboard.create_color'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.create_color') }}</h1>
        <a href="{{ route('admin.settings.colors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.colors.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="name">{{ __('admin-dashboard.color_name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="hex">{{ __('admin-dashboard.color_hex') }}</label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color @error('hex') is-invalid @enderror"
                               id="hex" name="hex" value="{{ old('hex', '#000000') }}" required>
                        <input type="text" class="form-control @error('hex') is-invalid @enderror"
                               id="hex_text" value="{{ old('hex', '#000000') }}" readonly>
                    </div>
                    @error('hex')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.create_color') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('hex').addEventListener('input', function(e) {
        document.getElementById('hex_text').value = e.target.value;
    });
</script>
@endsection
