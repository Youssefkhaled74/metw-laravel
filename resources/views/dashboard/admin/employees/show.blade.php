@extends('layouts.admin')

@section('title', __('admin-dashboard.employee_details'))
@section('page-title', __('admin-dashboard.employee_details'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">{{ __('admin-dashboard.employees') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.employee') }} #{{ $employee->id }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
    </a>
    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
    </a>
    <form action="{{ route('admin.employees.destroy', $employee->id) }}"
          method="POST" class="d-inline"
          onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_employee') }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
        </button>
    </form>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.employee_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('admin-dashboard.id') }}</th>
                            <td>{{ $employee->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.first_name') }}</th>
                            <td>{{ $employee->first_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.last_name') }}</th>
                            <td>{{ $employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.email') }}</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.phone') }}</th>
                            <td>{{ $employee->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.position') }}</th>
                            <td>{{ $employee->position ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.salary') }}</th>
                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($employee->salary, 2) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.hire_date') }}</th>
                            <td>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.created_at') }}</th>
                            <td>{{ $employee->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.updated_at') }}</th>
                            <td>{{ $employee->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
