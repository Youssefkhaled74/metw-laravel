@extends('layouts.vendor')

@section('title', __('vendor-dashboard.add_new_branch'))
@section('page-title', __('vendor-dashboard.add_new_branch'))

@section('content')
<style>
    .required-label::after {
        content: " *";
        color: red;
    }
    #map {
        height: 450px;
        width: 100%;
        border-radius: 8px;
        border: 2px solid #ddd;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('vendor.branches.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Branch Name -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label required-label">{{ __('vendor-dashboard.branch_name') }}</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
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
                                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
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
                                            required
                                            disabled>
                                        <option value="">{{ __('vendor-dashboard.select_city_first') }}</option>
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
                                            required
                                            disabled>
                                        <option value="">{{ __('vendor-dashboard.select_zone_first') }}</option>
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
                                           value="{{ old('street_main') }}"
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
                                           value="{{ old('street_sub') }}"
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
                                           value="{{ old('building') }}"
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
                                           value="{{ old('building_name') }}"
                                           placeholder="{{ __('vendor-dashboard.enter_building_name') }}">
                                    @error('building_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Floor -->
                                <div class="col-md-4 mb-3">
                                    <label for="floor" class="form-label">{{ __('vendor-dashboard.floor') }}</label>
                                    <input type="number"
                                           class="form-control @error('floor') is-invalid @enderror"
                                           id="floor"
                                           name="floor"
                                           value="{{ old('floor') }}"
                                           placeholder="{{ __('vendor-dashboard.enter_floor') }}">
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

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
                                <div id="map"></div>
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
                                           value="{{ old('latitude') }}"
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
                                           value="{{ old('longitude') }}"
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
                                       {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">{{ __('vendor-dashboard.active') }}</label>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('vendor.branches') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('vendor-dashboard.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('vendor-dashboard.create_branch') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8sZjSpTijQt3lC9CoIMr0F1izwoJrXjM&libraries=places&callback=initMap" async defer></script>

<script>
let map;
let marker;
let geocoder;
let autocomplete;

function initMap() {
    // Default location (Cairo, Egypt)
    const defaultLocation = { lat: 30.0444, lng: 31.2357 };

    // Get old values if exists
    const oldLat = parseFloat(document.getElementById('latitude').value) || defaultLocation.lat;
    const oldLng = parseFloat(document.getElementById('longitude').value) || defaultLocation.lng;
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

    // Add marker
    marker = new google.maps.Marker({
        position: initialLocation,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: '{{ __("vendor-dashboard.drag_marker") }}'
    });

    // Update coordinates when marker is dragged
    marker.addListener('dragend', function(event) {
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Add click listener to map
    map.addListener('click', function(event) {
        placeMarker(event.latLng);
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });

    // Initialize autocomplete search
    const searchBox = document.getElementById('search-box');
    autocomplete = new google.maps.places.Autocomplete(searchBox);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry || !place.geometry.location) {
            alert('{{ __("vendor-dashboard.no_location_found") }}');
            return;
        }

        // Update map and marker
        map.setCenter(place.geometry.location);
        map.setZoom(17);
        placeMarker(place.geometry.location);
        updateCoordinates(place.geometry.location.lat(), place.geometry.location.lng());
    });

    // Try to get user's current location
    if (navigator.geolocation && !document.getElementById('latitude').value) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                map.setCenter(userLocation);
                placeMarker(new google.maps.LatLng(userLocation.lat, userLocation.lng));
                updateCoordinates(userLocation.lat, userLocation.lng);
            },
            function() {
                console.log('Error: The Geolocation service failed.');
            }
        );
    }
}

function placeMarker(location) {
    marker.setPosition(location);
    map.panTo(location);
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
}

// State/City/Zone Selection
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const zoneSelect = document.getElementById('zone_id');

    // When state changes, load cities
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;

        citySelect.innerHTML = '<option value="">{{ __("vendor-dashboard.loading") }}...</option>';
        citySelect.disabled = true;
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
                        citySelect.innerHTML += `<option value="${city.id}">${name}</option>`;
                    });
                    citySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                });
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
                        zoneSelect.innerHTML += `<option value="${zone.id}">${name}</option>`;
                    });
                    zoneSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    zoneSelect.innerHTML = '<option value="">Error loading zones</option>';
                });
        }
    });
});
</script>
@endsection
