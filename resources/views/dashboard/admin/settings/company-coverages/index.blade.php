@extends('layouts.admin')

@section('title', __('admin-dashboard.coverage_management'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.coverage_management') }}</h1>
        <a href="{{ route('admin.settings.company-coverages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_coverage') }}
        </a>
    </div>

     {{-- @include('admin.partials.alerts') --}}

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('admin-dashboard.city') }}</th>
                            <th>{{ __('admin-dashboard.price') }}</th>
                            <th>{{ __('admin-dashboard.delivery_time') }}</th>
                            <th>{{ __('admin-dashboard.status') }}</th>
                            <th>{{ __('admin-dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coverages as $coverage)
                            <tr>
                                <td>{{ $coverage->id }}</td>
                                <td>{{ $coverage->city->name }}</td>
                                <td>{{ $coverage->price }}</td>
                                <td>{{ $coverage->delivery_time }}</td>
                                <td>
                                    <form action="{{ route('admin.settings.company-coverages.toggle-status', $coverage) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $coverage->is_active ? 'success' : 'danger' }}">
                                            {{ $coverage->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.settings.company-coverages.edit', $coverage) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
                                    </a>
                                    <form action="{{ route('admin.settings.company-coverages.destroy', $coverage) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(__('admin-dashboard.confirm_delete_coverage'))">
                                            <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('admin-dashboard.no_coverages_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $coverages->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
