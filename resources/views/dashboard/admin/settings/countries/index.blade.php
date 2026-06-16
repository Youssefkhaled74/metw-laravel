@extends('layouts.admin')

@section('title', __('admin-dashboard.countries_management'))
@section('page-title', __('admin-dashboard.countries_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.countries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_country') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin-dashboard.all_countries') }}</h5>
        </div>
        <div class="card-body">
            @if($countries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin-dashboard.name_en') }}</th>
                                <th>{{ __('admin-dashboard.name_ar') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.states_count') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countries as $country)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $country->name_en }}</td>
                                    <td>{{ $country->name_ar }}</td>
                                    <td>
                                        <form action="{{ route('admin.settings.countries.toggle-status', $country->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-{{ $country->is_active ? 'success' : 'danger' }}">
                                                {{ $country->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $country->states_count ?? $country->states()->count() }}
                                        </span>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $country->created_at])</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.countries.edit', $country->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> {{ __('admin-dashboard.edit') }}
                                            </a>
                                            <form action="{{ route('admin.settings.countries.destroy', $country->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_country') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> {{ __('admin-dashboard.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $countries->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('admin-dashboard.no_countries_found') }}</h5>
                    <p class="text-muted mb-0">{{ __('admin-dashboard.start_adding_country') }}</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.settings.countries.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_country') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
