@extends('layouts.admin')

@section('title', 'Add New Warehouse')
@section('page-title', 'Add New Warehouse')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.settings.warehouses.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Warehouse Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country_id" id="country" class="form-select" required>
                        <option value="">-- Select Country --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->{'name_' . app()->getLocale()} }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <select name="state_id" id="state" class="form-select" required disabled>
                        <option value="">-- Select State --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <select name="city_id" id="city" class="form-select" required disabled>
                        <option value="">-- Select City --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Zone</label>
                    <select name="zone_id" id="zone" class="form-select" disabled>
                        <option value="">-- Select Zone --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Street Name</label>
                    <input type="text" name="street_name" class="form-control" value="{{ old('street_name') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Building</label>
                    <input type="text" name="building" class="form-control" value="{{ old('building') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Floor</label>
                    <input type="text" name="floor" class="form-control" value="{{ old('floor') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Landmark</label>
                    <input type="text" name="landmark" class="form-control" value="{{ old('landmark') }}">
                </div>

                {{-- 🌍 خريطة جوجل / أبل --}}
                <div class="col-md-12">
                    <label class="form-label">Map Link (Google or Apple)</label>
                    <div class="input-group">
                        <input type="text" id="map_link" class="form-control" placeholder="Paste map link here (Google or Apple)">
                        <button type="button" id="open_map" class="btn btn-outline-secondary" disabled>Open Map</button>
                    </div>
                    <small class="text-muted">
                        Example: https://www.google.com/maps/place/30.0444,31.2357 or https://maps.apple.com/?ll=30.0444,31.2357
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}">
                </div>

                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_main" class="form-check-input" value="1" {{ old('is_main') ? 'checked' : '' }}>
                        <label class="form-check-label">Main Warehouse</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Warehouse
                </button>
                <a href="{{ route('admin.settings.warehouses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
<script>
/* ------------------------------
   🗺️ استخراج اللات واللونج من Google أو Apple Maps Link
------------------------------ */
const linkInput = document.getElementById('map_link');
const openBtn = document.getElementById('open_map');
const latInput = document.querySelector('input[name="latitude"]');
const lngInput = document.querySelector('input[name="longitude"]');

linkInput.addEventListener('input', function () {
    const link = this.value.trim();
    this.classList.remove('is-valid', 'is-invalid');
    openBtn.disabled = true;

    if (!link) return;

    const isGoogle = link.includes('google.com/maps') || link.includes('goo.gl/maps');
    const isApple  = link.includes('maps.apple.com');

    if (!isGoogle && !isApple) {
        this.classList.add('is-invalid');
        return;
    }

    let lat = null, lng = null;

    const patterns = [
        /@(-?\d+\.\d+),(-?\d+\.\d+)/,   // Google: @lat,lng
        /\/(-?\d+\.\d+),(-?\d+\.\d+)/,  // Google short
        /q=(-?\d+\.\d+),(-?\d+\.\d+)/,  // Google/Apple q param
        /ll=(-?\d+\.\d+),(-?\d+\.\d+)/  // Apple ll param
    ];

    for (const regex of patterns) {
        const match = link.match(regex);
        if (match) {
            lat = parseFloat(match[1]);
            lng = parseFloat(match[2]);
            break;
        }
    }

    if (lat && lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        this.classList.add('is-valid');
        openBtn.disabled = false;
    } else {
        this.classList.add('is-invalid');
    }
});

openBtn.addEventListener('click', function () {
    const link = linkInput.value.trim();
    if (link) window.open(link, '_blank');
});

/* ------------------------------
   🌍 AJAX for dependent dropdowns
------------------------------ */
document.getElementById('country').addEventListener('change', function() {
    const countryId = this.value;
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');
    const zoneSelect = document.getElementById('zone');

    stateSelect.innerHTML = '<option value="">Loading...</option>';
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    zoneSelect.innerHTML = '<option value="">-- Select Zone --</option>';
    stateSelect.disabled = true;
    citySelect.disabled = true;
    zoneSelect.disabled = true;

    if (countryId) {
        fetch(`/admin/settings/get-states/${countryId}`)
            .then(res => res.json())
            .then(data => {
                stateSelect.innerHTML = '<option value="">-- Select State --</option>';
                data.forEach(state => {
                    const name = state[`name_${'{{ app()->getLocale() }}'}`] ?? state.name_en;
                    stateSelect.innerHTML += `<option value="${state.id}">${name}</option>`;
                });
                stateSelect.disabled = false;
            });
    }
});

document.getElementById('state').addEventListener('change', function() {
    const stateId = this.value;
    const citySelect = document.getElementById('city');
    const zoneSelect = document.getElementById('zone');

    citySelect.innerHTML = '<option value="">Loading...</option>';
    zoneSelect.innerHTML = '<option value="">-- Select Zone --</option>';
    citySelect.disabled = true;
    zoneSelect.disabled = true;

    if (stateId) {
        fetch(`/admin/settings/get-cities/${stateId}`)
            .then(res => res.json())
            .then(data => {
                citySelect.innerHTML = '<option value="">-- Select City --</option>';
                data.forEach(city => {
                    const name = city[`name_${'{{ app()->getLocale() }}'}`] ?? city.name_en;
                    citySelect.innerHTML += `<option value="${city.id}">${name}</option>`;
                });
                citySelect.disabled = false;
            });
    }
});

document.getElementById('city').addEventListener('change', function() {
    const cityId = this.value;
    const zoneSelect = document.getElementById('zone');

    zoneSelect.innerHTML = '<option value="">Loading...</option>';
    zoneSelect.disabled = true;

    if (cityId) {
        fetch(`/admin/settings/get-zones/${cityId}`)
            .then(res => res.json())
            .then(data => {
                zoneSelect.innerHTML = '<option value="">-- Select Zone --</option>';
                data.forEach(zone => {
                    const name = zone[`name_${'{{ app()->getLocale() }}'}`] ?? zone.name_en;
                    zoneSelect.innerHTML += `<option value="${zone.id}">${name}</option>`;
                });
                zoneSelect.disabled = false;
            });
    }
});
</script>
@endsection
