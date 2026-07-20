@extends('layouts.admin')

@section('title', __('admin-dashboard.management_contact_info'))
@section('page-title', __('admin-dashboard.management_contact_info'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.management_contact_info') }}</h5>
                <p class="mb-0 text-muted small">{{ __('admin-dashboard.management_contact_subtitle') }}</p>
            </div>
            <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                <i class="fas fa-users me-1"></i>
                {{ $employees->count() }} {{ __('admin-dashboard.employees') }}
            </span>
        </div>
    </div>

    <div class="card-body">
        @if($employees->count() > 0)
            <div class="row g-4">
                @foreach($employees as $employee)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border contact-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="contact-avatar rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                                        @if($employee->position)
                                            <small class="text-muted">{{ $employee->position }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="contact-info-list">
                                    @if($employee->email)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fas fa-envelope text-primary"></i>
                                            <a href="mailto:{{ $employee->email }}" class="text-decoration-none">{{ $employee->email }}</a>
                                        </div>
                                    @endif
                                    @if($employee->phone)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fas fa-phone text-success"></i>
                                            <a href="tel:{{ $employee->phone }}" class="text-decoration-none" dir="ltr">{{ $employee->phone }}</a>
                                        </div>
                                    @endif
                                    @if($employee->employee_number)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="fas fa-id-badge text-secondary"></i>
                                            <span>{{ $employee->employee_number }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">{{ __('admin-dashboard.no_employees_found') }}</h5>
            </div>
        @endif
    </div>
</div>

<style>
    .contact-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .contact-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .contact-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        font-size: 1.1rem;
    }
    .contact-info-list {
        font-size: 0.88rem;
    }
    .contact-info-list a:hover {
        text-decoration: underline !important;
    }
</style>
@endsection
