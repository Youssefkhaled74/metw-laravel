@extends('layouts.admin')

@section('title', __('admin-dashboard.permissions'))
@section('page-title', __('admin-dashboard.permissions_management') ?? 'Permissions Management')

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_permission') ?? 'Add Permission' }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin-dashboard.all_permissions') ?? 'All Permissions' }}</h5>
        </div>
        <div class="card-body">
            @if ($permissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin-dashboard.name') ?? 'Name' }}</th>
                            <th>{{ __('admin-dashboard.guard') ?? 'Guard' }}</th>
                            <th>{{ __('admin-dashboard.created_at') }}</th>
                            <th>{{ __('admin-dashboard.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td><strong>{{ $permission->id }}</strong></td>
                                <td>{{ __('permissions.' . $permission->name) }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td>{{ $permission->created_at?->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.permissions.show', $permission->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> {{ __('admin-dashboard.view') }}
                                    </a>
                                    <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
                                    </a>
                                    <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_permission') ?? 'Are you sure you want to delete this permission?' }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $permissions->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('admin-dashboard.no_permissions_found') ?? 'No permissions found' }}</h5>
                </div>
            @endif
        </div>
    </div>
@endsection


