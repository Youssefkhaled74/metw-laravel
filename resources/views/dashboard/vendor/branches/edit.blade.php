@extends('layouts.vendor')

@section('title', __('vendor-dashboard.edit_branch'))
@section('page-title', __('vendor-dashboard.edit_branch'))

@section('content')
<style>
    .required-label::after {
        content: " *";
        color: red;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('vendor.branches.update', $branch->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <!-- Branch Name -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label required-label">{{ __('vendor-dashboard.branch_name') }}</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $branch->name) }}"
                                       placeholder="{{ __('vendor-dashboard.enter_branch_name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Section -->
                        <div class="mt-4">
                            <h5>{{ __('vendor-dashboard.location_details') }}</h5>
                            <hr>

                            <div class="row">
                                <!-- State -->
                                <div class="col-md-4 mb-3">
                                    <label for="state_id" class="form-label required-label">{{ __('vendor-dashboard.state') }}</label>
                                    <select class="form-select @error('state_id') is-invalid @enderror"
                                            id="state_id"
                                            name="state_id"
                                            required>
                                        <option value="">{{ __('vendor-dashboard.select_state') }}</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ old('state_id', $branch->state_id) == $state->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $state->name_ar : $state->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- City -->
                                <div class="col-md-4 mb-3">
                                    <label for="city_id" class="form-label required-label">{{ __('vendor-dashboard.city') }}</label>
                                    <select class="form-select @error('city_id') is-invalid @enderror"
                                            id="city_id"
                                            name="city_id"
                                            required>
                                        <option value="">{{ __('vendor-dashboard.select_city') }}</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ old('city_id', $branch->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $city->name_ar : $city->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Zone -->
                                <div class="col-md-4 mb-3">
                                    <label for="zone_id" class="form-label required-label">{{ __('vendor-dashboard.zone') }}</label>
                                    <select class="form-select @error('zone_id') is-invalid @enderror"
                                            id="zone_id"
                                            name="zone_id"
                                            required>
                                        <option value="">{{ __('vendor-dashboard.select_zone') }}</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}"
                                                {{ old('zone_id', $branch->zone_id) == $zone->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $zone->name_ar : $zone->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zone_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="mt-4">
                            <h5>{{ __('vendor-dashboard.address_details') }}</h5>
                            <hr>

                            <div class="row">
                                <!-- Main Street -->
                                <div class="col-md-6 mb-3">
                                    <label for="street_main" class="form-label required-label">{{ __('vendor-dashboard.main_street') }}</label>
                                    <input type="text"
                                           class="form-control @error('street_main') is-invalid @enderror"
                                           id="street_main"
                                           name="street_main"
                                           value="{{ old('street_main', $branch->street_main) }}"
                                           placeholder="{{ __('vendor-dashboard.enter_main_street') }}"
                                           required>
                                    @error('street_main')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sub Street -->
                                <div class="col-md-6 mb-3">
                                    <label for="street_sub" class="form-label">{{ __('vendor-dashboard.sub_street') }}</label>
                                    <input type="text"
                                           class="form-control @error('street_sub') is-invalid @enderror"
                                           id="street_sub"
                                           name="street_sub"
                                           value="{{ old('street_sub', $branch->street_sub) }}"
                                           placeholder="{{ __('vendor-dashboard.enter_sub_street') }}">
                                    @error('street_sub')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Building Number -->
                                <div class="col-md-4 mb-3">
                                    <label for="building" class="form-label required-label">{{ __('vendor-dashboard.building_number') }}</label>
                                    <input type="number"
                                           class="form-control @error('building') is-invalid @enderror"
                                           id="building"
                                           name="building"
                                           value="{{ old('building', $branch->building) }}"
                                           placeholder="{{ __('vendor-dashboard.enter_building_number') }}"
                                           required>
                                    @error('building')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Building Name -->
                                <div class="col-md-4 mb-3">
                                    <label for="building_name" class="form-label">{{ __('vendor-dashboard.building_name') }}</label>
                                    <input type="text"
                                           class="form-control @error('building_name') is-invalid @enderror"
                                           id="building_name"
                                           name="building_name"
                                           value="{{ old('building_name', $branch->building_name) }}"
                                           placeholder="{{ __('vendor-dashboard.enter_building_name') }}">
                                    @error('building_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Floor -->
                                <div class="col-md-4 mb-3">
                                    <label for="number" class="form-label">{{ __('vendor-dashboard.floor') }}</label>
                                    <input type="text"
                                           class="form-control @error('floor') is-invalid @enderror"
                                           id="floor"
                                           name="floor"
                                           value="{{ old('floor', $branch->floor) }}"
                                           placeholder="{{ __('vendor-dashboard.enter_floor') }}">
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Map Coordinates -->
                        <!-- Map Coordinates -->
                        <div class="mt-4">
                            <h5>{{ __('vendor-dashboard.map_coordinates') }}</h5>
                            <hr>

                            <!-- Search Box -->
                            <div class="mb-3">
                                <label for="search-box" class="form-label">
                                    <i class="fas fa-search"></i> {{ __('vendor-dashboard.search_location') }}
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="search-box"
                                    placeholder="{{ __('vendor-dashboard.search_location_placeholder') }}">
                            </div>

                            <!-- Map Container -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('vendor-dashboard.select_location_on_map') }}</label>
                                <div id="map" style="height: 450px; width: 100%; border-radius: 8px; border: 2px solid #ddd;"></div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> {{ __('vendor-dashboard.map_help') }}
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> {{ __('vendor-dashboard.latitude') }}
                                    </label>
                                    <input type="text"
                                        class="form-control @error('latitude') is-invalid @enderror"
                                        id="latitude"
                                        name="latitude"
                                        value="{{ old('latitude', $branch->latitude) }}"
                                        placeholder="30.0444"
                                        readonly>
                                    <small class="text-muted">{{ __('vendor-dashboard.latitude_help') }}</small>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> {{ __('vendor-dashboard.longitude') }}
                                    </label>
                                    <input type="text"
                                        class="form-control @error('longitude') is-invalid @enderror"
                                        id="longitude"
                                        name="longitude"
                                        value="{{ old('longitude', $branch->longitude) }}"
                                        placeholder="31.2357"
                                        readonly>
                                    <small class="text-muted">{{ __('vendor-dashboard.longitude_help') }}</small>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <!-- Status Section -->
                        <div class="mt-4">
                            <h5>{{ __('vendor-dashboard.branch_status') }}</h5>
                            <hr>

                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="status"
                                       name="status"
                                       value="1"
                                       {{ old('status', $branch->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">{{ __('vendor-dashboard.active') }}</label>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('vendor.branches') }}" class="btn btn-secondary">{{ __('vendor-dashboard.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('vendor-dashboard.update_branch') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const zoneSelect = document.getElementById('zone_id');

    // Store original values
    const originalStateId = "{{ old('state_id', $branch->state_id) }}";
    const originalCityId = "{{ old('city_id', $branch->city_id) }}";
    const originalZoneId = "{{ old('zone_id', $branch->zone_id) }}";

    // When state changes, load cities
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;

        citySelect.innerHTML = '<option value="">{{ __("vendor-dashboard.loading") }}...</option>';
        zoneSelect.innerHTML = '<option value="">{{ __("vendor-dashboard.select_zone_first") }}</option>';
        zoneSelect.disabled = true;

        if (stateId) {
            fetch(`{{ route('vendor.api.cities', '') }}/${stateId}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">{{ __("vendor-dashboard.select_city") }}</option>';
                    data.forEach(city => {
                        const locale = '{{ app()->getLocale() }}';
                        const name = locale === 'ar' ? city.name_ar : city.name_en;
                        const selected = city.id == originalCityId ? 'selected' : '';
                        citySelect.innerHTML += `<option value="${city.id}" ${selected}>${name}</option>`;
                    });

                    // Trigger change to load zones if city is selected
                    if (originalCityId) {
                        citySelect.dispatchEvent(new Event('change'));
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // When city changes, load zones
    citySelect.addEventListener('change', function() {
        const cityId = this.value;

        zoneSelect.innerHTML = '<option value="">{{ __("vendor-dashboard.loading") }}...</option>';
        zoneSelect.disabled = true;

        if (cityId) {
            fetch(`{{ route('vendor.api.zones', '') }}/${cityId}`)
                .then(response => response.json())
                .then(data => {
                    zoneSelect.innerHTML = '<option value="">{{ __("vendor-dashboard.select_zone") }}</option>';
                    data.forEach(zone => {
                        const locale = '{{ app()->getLocale() }}';
                        const name = locale === 'ar' ? zone.name_ar : zone.name_en;
                        const selected = zone.id == originalZoneId ? 'selected' : '';
                        zoneSelect.innerHTML += `<option value="${zone.id}" ${selected}>${name}</option>`;
                    });
                    zoneSelect.disabled = false;
                })
                .catch(error => console.error('Error:', error));
        }
    });
});
</script>
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8sZjSpTijQt3lC9CoIMr0F1izwoJrXjM&libraries=places&callback=initMap" async defer></script>

<script>
let map;
let marker;
let geocoder;
let autocomplete;

function initMap() {
    const oldLat = parseFloat(document.getElementById('latitude').value) || 30.0444;
    const oldLng = parseFloat(document.getElementById('longitude').value) || 31.2357;
    const initialLocation = { lat: oldLat, lng: oldLng };

    // Initialize map
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: initialLocation,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        zoomControl: true,
    });

    // Initialize geocoder
    geocoder = new google.maps.Geocoder();

    // Add draggable marker
    marker = new google.maps.Marker({
        position: initialLocation,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: '{{ __("vendor-dashboard.drag_marker") }}'
    });

    // Update coordinates when marker dragged
    marker.addListener('dragend', function(event) {
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Click on map sets marker
    map.addListener('click', function(event) {
        placeMarker(event.latLng);
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Search Box
    const searchBox = document.getElementById('search-box');
    autocomplete = new google.maps.places.Autocomplete(searchBox);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) {
            alert('{{ __("vendor-dashboard.no_location_found") }}');
            return;
        }

        map.setCenter(place.geometry.location);
        map.setZoom(17);
        placeMarker(place.geometry.location);
        updateCoordinates(place.geometry.location.lat(), place.geometry.location.lng());
    });
}

function placeMarker(location) {
    marker.setPosition(location);
    map.panTo(location);
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
}
</script>

@endsection
