@extends('layouts.admin')

@section('title', __('admin-dashboard.no_permission'))
@section('page-title', __('admin-dashboard.no_permission'))

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm">
                <div class="card-body">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h3 class="mb-3">{{ __('admin-dashboard.access_denied') }}</h3>
                    <p class="text-muted">{{ __('admin-dashboard.you_do_not_have_permission_to_view_this_page') }}</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.go_back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
