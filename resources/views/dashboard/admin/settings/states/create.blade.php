@extends('layouts.admin')

@section('title', __('admin-dashboard.create_state'))
@section('page-title', __('admin-dashboard.create_state'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.states.index') }}">{{ __('admin-dashboard.states') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_state') }}</li>
@endsection

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card form-card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">{{ __('admin-dashboard.state_details') }}</h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('admin.settings.states.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name_en" class="form-label required">{{ __('admin-dashboard.state_name_en') }}</label>
                                <input type="text"
                                       class="form-control modern-input @error('name_en') is-invalid @enderror"
                                       id="name_en"
                                       name="name_en"
                                       value="{{ old('name_en') }}"
                                       required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name_ar" class="form-label required">{{ __('admin-dashboard.state_name_ar') }}</label>
                                <input type="text"
                                       class="form-control modern-input @error('name_ar') is-invalid @enderror"
                                       id="name_ar"
                                       name="name_ar"
                                       value="{{ old('name_ar') }}"
                                       required>
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 mb-4">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-between">
                            <a href="{{ route('admin.settings.states.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('admin-dashboard.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-save">
                                <i class="fas fa-save me-1"></i> {{ __('admin-dashboard.create_state') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card form-side-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small mb-3">{{ app()->getLocale() === 'ar' ? 'إعداد ثابت' : 'Fixed Setup' }}</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="country-badge"><i class="fas fa-flag"></i></span>
                        <strong>{{ app()->getLocale() === 'ar' ? 'الدولة: مصر' : 'Country: Egypt' }}</strong>
                    </div>
                    <p class="text-muted mb-0 small">
                        {{ app()->getLocale() === 'ar'
                            ? 'تم تثبيت الدولة تلقائيًا على مصر لجميع المحافظات.'
                            : 'Country is automatically fixed to Egypt for all states.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .required:after { content:" *"; color:red; }

    .form-card,
    .form-side-card {
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
    }

    .modern-input {
        min-height: 44px;
        border-radius: 12px;
        border-color: #e2e8f0;
    }

    .modern-input:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.13);
    }

    .btn-save {
        box-shadow: 0 10px 22px rgba(59, 130, 246, 0.2);
    }

    .country-badge {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
    }
</style>
@endpush
