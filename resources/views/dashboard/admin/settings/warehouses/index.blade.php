@extends('layouts.admin')

@section('title', 'Warehouse Management')
@section('page-title', 'Warehouse Management')

@section('page-actions')
    <a href="{{ route('admin.settings.warehouses.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Warehouse
    </a>
@endsection


@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Warehouse</h5>
    </div>
    <div class="card-body">
        @if($warehouses->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Main</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouses as $warehouse)
                            <tr>
                                <td>{{ $warehouse->name }}</td>
                                <td>{{ $warehouse->phone ?? '-' }}</td>
                                <td>
                                    {{ optional($warehouse->zone)->{"name_" . app()->getLocale()} ?? '' }},
                                    {{ optional($warehouse->city)->{"name_" . app()->getLocale()} ?? '' }},
                                    {{ optional($warehouse->state)->{"name_" . app()->getLocale()} ?? '' }},
                                    {{ optional($warehouse->country)->{"name_" . app()->getLocale()} ?? '' }}
                                </td>
                                <td>
                                    <form action="{{ route('admin.settings.warehouses.toggle-status', $warehouse->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-{{ $warehouse->is_main ? 'success' : 'secondary' }}">
                                            {{ $warehouse->is_main ? 'Main' : 'Not Main' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.settings.warehouses.edit', $warehouse->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.settings.warehouses.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $warehouses->links('pagination::bootstrap-5') }}
        @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-store fa-3x mb-3"></i>
                <p>No warehouses found</p>
                <a href="{{ route('admin.settings.warehouses.create') }}" class="btn btn-primary">Add Warehouse</a>
            </div>
        @endif
    </div>
</div>
@endsection
