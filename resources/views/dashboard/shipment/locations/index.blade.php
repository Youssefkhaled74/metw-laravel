@extends('layouts.shipment')

@section('title', __('shipment-dashboard.locations_management'))
@section('page-title', __('shipment-dashboard.locations_management'))

@section('page-actions')
    {{-- <a href="{{ route('shipment.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a> --}}
    <a href="{{ route('shipment.locations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> @lang('shipment-dashboard.add_location')
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('shipment-dashboard.all_locations')</h5>
        </div>
        <div class="card-body">
            @if ($locations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>@lang('shipment-dashboard.name')</th>
                                <th>@lang('shipment-dashboard.type_label')</th>
                                <th>@lang('shipment-dashboard.parent')</th>
                                <th>@lang('shipment-dashboard.path')</th>
                                <th>@lang('shipment-dashboard.status')</th>
                                <th>@lang('shipment-dashboard.created_at')</th>
                                <th>@lang('shipment-dashboard.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locations as $location)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $location->name }}</strong></td>
                                    <td>{{ ucfirst($location->type->value ?? $location->type) }}</td>
                                    <td>{{ $location->parent?->name ?? '—' }}</td>
                                    <td>{{ $location->path ?? '—' }}</td>
                                    <td>
                                        <form action="{{ route('shipment.locations.toggle-status', $location->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-{{ $location->is_active ? 'success' : 'danger' }}">
                                                {{ $location->is_active ? __('shipment-dashboard.active') : __('shipment-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $location->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('shipment.locations.edit', $location->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> @lang('shipment-dashboard.edit')
                                            </a>
                                            <form action="{{ route('shipment.locations.destroy', $location->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm(@js(__('shipment-dashboard.confirm_delete_location')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> @lang('shipment-dashboard.delete')
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $locations->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">@lang('shipment-dashboard.no_locations_found')</h5>
                    <p class="text-muted">@lang('shipment-dashboard.no_locations_yet')</p>
                </div>
            @endif
        </div>
    </div>
@endsection
