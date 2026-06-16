@extends('layouts.admin')

@section('title', __('admin-dashboard.permission_details') ?? 'Permission Details')
@section('page-title', __('admin-dashboard.permission_details') ?? 'Permission Details')

@section('page-actions')
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
    </a>
    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
    </a>
    <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
          method="POST" class="d-inline"
          onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_permission') ?? 'Are you sure you want to delete this permission?' }}');">
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
                    <h5 class="mb-0">{{ __('admin-dashboard.permission_information') ?? 'Permission Information' }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('admin-dashboard.id') }}</th>
                            <td>{{ $permission->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.name') ?? 'Name' }}</th>
                            <td>{{ $permission->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.guard') ?? 'Guard' }}</th>
                            <td>{{ $permission->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.created_at') }}</th>
                            <td>{{ $permission->created_at?->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin-dashboard.updated_at') }}</th>
                            <td>{{ $permission->updated_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection


