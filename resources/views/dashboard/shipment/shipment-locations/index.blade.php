@extends('layouts.shipment')

@section('title', __('shipment-dashboard.shipment_locations'))
@section('page-title', __('shipment-dashboard.shipment_locations'))

@section('page-actions')
    <a href="{{ route('shipment.shipment-locations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> @lang('shipment-dashboard.add_shipment_location')
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">@lang('shipment-dashboard.all_shipment_locations')</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $locations->count() }} / {{ $locations->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('shipment.shipment-locations.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالدولة أو المحافظة أو المدينة...' : 'Search by country, state, or city...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <select name="is_active" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('is_active', 'all') === 'all' ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All statuses' }}
                        </option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                            @lang('shipment-dashboard.active')
                        </option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                            @lang('shipment-dashboard.inactive')
                        </option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('shipment.shipment-locations.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body p-0 table-wrap">
            @if ($locations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $countryDir = request('sort_by') === 'country' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.shipment-locations.index', array_merge(request()->except('page'), ['sort_by' => 'country', 'sort_dir' => $countryDir])) }}">
                                        <span>@lang('shipment-dashboard.country')</span>
                                        <i class="fas {{ request('sort_by') === 'country' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $stateDir = request('sort_by') === 'state' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.shipment-locations.index', array_merge(request()->except('page'), ['sort_by' => 'state', 'sort_dir' => $stateDir])) }}">
                                        <span>@lang('shipment-dashboard.state')</span>
                                        <i class="fas {{ request('sort_by') === 'state' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $cityDir = request('sort_by') === 'city' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.shipment-locations.index', array_merge(request()->except('page'), ['sort_by' => 'city', 'sort_dir' => $cityDir])) }}">
                                        <span>@lang('shipment-dashboard.city')</span>
                                        <i class="fas {{ request('sort_by') === 'city' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>@lang('shipment-dashboard.zone')</th>
                                <th>@lang('shipment-dashboard.status')</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.shipment-locations.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>@lang('shipment-dashboard.created_at')</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>@lang('shipment-dashboard.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locations as $location)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <!-- Countries -->
                                    <td>
                                        @if(!empty($location->country))
                                            @foreach($location->country as $countryId)
                                                @php
                                                    $country = $countriesMap->get($countryId);
                                                    $countryName = $country ? ($country->{'name_' . app()->getLocale()} ?? $country->name_en ?? $country->name ?? '-') : '-';
                                                @endphp
                                                <span class="count-pill packages-pill">{{ $countryName }}</span>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <!-- States -->
                                    <td>
                                        @if(!empty($location->state))
                                            @foreach($location->state as $stateId)
                                                @php
                                                    $state = $statesMap->get($stateId);
                                                    $stateName = $state ? ($state->{'name_' . app()->getLocale()} ?? $state->name_en ?? $state->name ?? '-') : '-';
                                                @endphp
                                                <span class="count-pill orders-pill">{{ $stateName }}</span>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <!-- Cities -->
                                    <td>
                                        @if(!empty($location->city))
                                            @foreach($location->city as $cityId)
                                                @php
                                                    $city = $citiesMap->get($cityId);
                                                    $cityName = $city ? ($city->{'name_' . app()->getLocale()} ?? $city->name_en ?? $city->name ?? '-') : '-';
                                                @endphp
                                                <span class="count-pill packages-pill">{{ $cityName }}</span>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <!-- Zones -->
                                    <td>
                                        @if(!empty($location->zone))
                                            @foreach($location->zone as $zoneId)
                                                @php
                                                    $zone = $zonesMap->get($zoneId);
                                                    $zoneName = $zone ? ($zone->{'name_' . app()->getLocale()} ?? $zone->name_en ?? $zone->name ?? '-') : '-';
                                                @endphp
                                                <span class="count-pill orders-pill">{{ $zoneName }}</span>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('shipment.shipment-locations.toggle-status', $location->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm action-icon-btn {{ $location->is_active ? 'btn-success text-white' : 'btn-danger text-white' }}"
                                                    title="{{ $location->is_active ? __('shipment-dashboard.active') : __('shipment-dashboard.inactive') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas {{ $location->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $location->created_at])</td>

                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('shipment.shipment-locations.edit', $location->id) }}"
                                            class="btn btn-sm btn-warning text-white action-icon-btn"
                                            title="@lang('shipment-dashboard.edit')"
                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('shipment.shipment-locations.destroy', $location->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm(@js(__('shipment-dashboard.confirm_delete_location')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn"
                                                    title="@lang('shipment-dashboard.delete')"
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-trash"></i>
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
                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $locations->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-map-marker-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">@lang('shipment-dashboard.no_shipment_locations_found')</h5>
                        <p class="text-muted mb-0">@lang('shipment-dashboard.no_shipment_locations_yet')</p>
                        @if(request('search') || request('is_active') !== 'all')
                            <a href="{{ route('shipment.shipment-locations.index') }}" class="btn btn-outline-primary btn-sm mt-3">
                                <i class="fas fa-undo me-1"></i> @lang('shipment-dashboard.clear_filters')
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
