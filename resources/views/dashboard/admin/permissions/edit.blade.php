@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_permission') ?? 'Edit Permission')
@section('page-title', __('admin-dashboard.edit_permission') ?? 'Edit Permission')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.edit_permission_details') ?? 'Edit Permission Details' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- NAME (permission key) --}}
                        <div class="mb-3">
                            <label for="name" class="form-label required">{{ __('admin-dashboard.name') ?? 'Name' }}</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $permission->name) }}"
                                   required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- LABEL EN --}}
                        <div class="mb-3">
                            <label for="label_en" class="form-label">{{ __('admin-dashboard.permission_label_en') ?? 'Permission label (English)' }}</label>
                            <input type="text"
                                   class="form-control @error('label_en') is-invalid @enderror"
                                   id="label_en"
                                   name="label_en"
                                   value="{{ old('label_en', $labelEn) }}">
                            @error('label_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- LABEL AR --}}
                        <div class="mb-3">
                            <label for="label_ar" class="form-label">{{ __('admin-dashboard.permission_label_ar') ?? 'Permission label (Arabic)' }}</label>
                            <input type="text"
                                   class="form-control @error('label_ar') is-invalid @enderror"
                                   id="label_ar"
                                   name="label_ar"
                                   value="{{ old('label_ar', $labelAr) }}">
                            @error('label_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.update_permission') ?? 'Update Permission' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .required:after {
            content: " *";
            color: red;
        }
    </style>
@endpush


