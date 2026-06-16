@extends('layouts.admin')
@section('title', __('admin-dashboard.edit_main_category'))
@section('content')
    <div class="container-fluid">
        <h4>{{ __('admin-dashboard.edit_main_category') }}</h4>
        <form action="{{ route('admin.settings.main-categories.update', $mainCategory->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PATCH')
                <div class="mb-3">
                    <label for="name_en" class="form-label">{{ __('admin-dashboard.name_en') }}</label>
                    <input type="text" class="form-control @error('translations.en.name') is-invalid @enderror"
                        id="name_en" name="translations[en][name]"
                        value="{{ old('translations.en.name', optional($mainCategory->translation('en'))->name) }}" required>
                    @error('translations.en.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug_en" class="form-label">{{ __('admin-dashboard.slug_en') }}</label>
                    <input type="text" class="form-control @error('translations.en.slug') is-invalid @enderror"
                        id="slug_en" name="translations[en][slug]"
                        value="{{ old('translations.en.slug', optional($mainCategory->translation('en'))->slug) }}">
                    @error('translations.en.slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name_ar" class="form-label">{{ __('admin-dashboard.name_ar') }}</label>
                    <input type="text" class="form-control @error('translations.ar.name') is-invalid @enderror"
                        id="name_ar" name="translations[ar][name]"
                        value="{{ old('translations.ar.name', optional($mainCategory->translation('ar'))->name) }}" required>
                    @error('translations.ar.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug_ar" class="form-label">{{ __('admin-dashboard.slug_ar') }}</label>
                    <input type="text" class="form-control @error('translations.ar.slug') is-invalid @enderror"
                        id="slug_ar" name="translations[ar][slug]"
                        value="{{ old('translations.ar.slug', optional($mainCategory->translation('ar'))->slug) }}">
                    @error('translations.ar.slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            <div class="mb-3">
                <label for="image" class="form-label">{{ __('admin-dashboard.image') }}</label>
                @if ($mainCategory->image)
                    <div class="mb-2"><img src="{{ asset( $mainCategory->image) }}" width="80"></div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                    name="image">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $mainCategory->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.save') }}</button>
            <a href="{{ route('admin.settings.main-categories.index') }}" class="btn btn-secondary">{{ __('admin-dashboard.cancel') }}</a>
        </form>
    </div>
@endsection
