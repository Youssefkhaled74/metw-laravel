@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_employee'))
@section('page-title', __('admin-dashboard.edit_employee'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">{{ __('admin-dashboard.employees') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.edit_employee') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('admin-dashboard.edit_employee_details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- First Name --}}
                    <div class="mb-3">
                        <label for="first_name" class="form-label required">{{ __('admin-dashboard.first_name') }}</label>
                        <input type="text"
                               class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name"
                               name="first_name"
                               value="{{ old('first_name', $employee->first_name) }}"
                               required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div class="mb-3">
                        <label for="last_name" class="form-label required">{{ __('admin-dashboard.last_name') }}</label>
                        <input type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name"
                               name="last_name"
                               value="{{ old('last_name', $employee->last_name) }}"
                               required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label required">{{ __('admin-dashboard.email') }}</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $employee->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label">{{ __('admin-dashboard.phone') }}</label>
                        <input type="text"
                               class="form-control @error('phone') is-invalid @enderror"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $employee->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Position --}}
                    <div class="mb-3">
                        <label for="position" class="form-label">{{ __('admin-dashboard.position') }}</label>
                        <input type="text"
                               class="form-control @error('position') is-invalid @enderror"
                               id="position"
                               name="position"
                               value="{{ old('position', $employee->position) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hire Date --}}
                    <div class="mb-3">
                        <label for="hire_date" class="form-label">{{ __('admin-dashboard.hire_date') }}</label>
                        <input type="date"
                               class="form-control @error('hire_date') is-invalid @enderror"
                               id="hire_date"
                               name="hire_date"
                               value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}">
                        @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('admin-dashboard.new_password_optional') }}</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="{{ __('admin-dashboard.leave_blank_password') }}">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('admin-dashboard.confirm_new_password') }}</label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="{{ __('admin-dashboard.reenter_password') }}">
                    </div>

                    {{-- Roles --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin-dashboard.assign_roles') }}</label>
                        <select name="roles[]" id="roles" class="form-select select2 @error('roles') is-invalid @enderror" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ in_array($role->name, old('roles', $employee->getRoleNames()->toArray())) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @include('dashboard.admin.employees._permissions-matrix', [
                        'permissions' => $permissions,
                        'selectedPermissions' => old('permissions', $employee->getDirectPermissions()->pluck('name')->toArray()),
                    ])


                    {{-- Buttons --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('admin-dashboard.update_employee') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        placeholder: "{{ __('Select options') }}",
        allowClear: true,
        width: '100%'
    });
});
</script>

@endsection

@push('styles')
<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>
@endpush
