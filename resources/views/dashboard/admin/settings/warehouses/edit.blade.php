@extends('layouts.admin')

@section('title', 'Edit Warehouse')
@section('page-title', 'Edit Warehouse')



@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.settings.warehouses.update', $warehouse->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Warehouse Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $warehouse->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $warehouse->phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country_id" id="country" class="form-select" required>
                        <option value="">-- Select Country --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ $warehouse->country_id == $country->id ? 'selected' : '' }}>
                                {{ $country->{'name_' . app()->getLocale()} }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <select name="state_id" id="state" class="form-select" required>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ $warehouse->state_id == $state->id ? 'selected' : '' }}>
                                {{ $state->{'name_' . app()->getLocale()} }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <select name="city_id" id="city" class="form-select" required>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $warehouse->city_id == $city->id ? 'selected' : '' }}>
                                {{ $city->{'name_' . app()->getLocale()} }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Zone</label>
                    <select name="zone_id" id="zone" class="form-select">
                        <option value="">-- Select Zone --</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ $warehouse->zone_id == $zone->id ? 'selected' : '' }}>
                                {{ $zone->{'name_' . app()->getLocale()} }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Street Name</label>
                    <input type="text" name="street_name" class="form-control" value="{{ old('street_name', $warehouse->street_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Building</label>
                    <input type="text" name="building" class="form-control" value="{{ old('building', $warehouse->building) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Floor</label>
                    <input type="text" name="floor" class="form-control" value="{{ old('floor', $warehouse->floor) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Landmark</label>
                    <input type="text" name="landmark" class="form-control" value="{{ old('landmark', $warehouse->landmark) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $warehouse->latitude) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $warehouse->longitude) }}">
                </div>

                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_main" class="form-check-input" value="1" {{ $warehouse->is_main ? 'checked' : '' }}>
                        <label class="form-check-label">Main Warehouse</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Warehouse
                </button>
                <a href="{{ route('admin.settings.warehouses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const stateSelect   = document.getElementById('state');
    const citySelect    = document.getElementById('city');
    const zoneSelect    = document.getElementById('zone');

    // القيم القديمة (من السيرفر)
    const oldStateId = "{{ $warehouse->state_id }}";
    const oldCityId  = "{{ $warehouse->city_id }}";
    const oldZoneId  = "{{ $warehouse->zone_id }}";

    // لما المستخدم يغيّر الدولة
    countrySelect.addEventListener('change', function() {
        const countryId = this.value;
        if (!countryId) return;

        fetch(`/admin/settings/get-states/${countryId}`)
            .then(res => res.json())
            .then(data => {
                fillSelect(stateSelect, data, oldStateId);
                stateSelect.disabled = false;
            });
    });

    // لما المستخدم يغيّر المحافظة
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        if (!stateId) return;

        fetch(`/admin/settings/get-cities/${stateId}`)
            .then(res => res.json())
            .then(data => {
                fillSelect(citySelect, data, oldCityId);
                citySelect.disabled = false;
            });
    });

    // لما المستخدم يغيّر المدينة
    citySelect.addEventListener('change', function() {
        const cityId = this.value;
        if (!cityId) return;

        fetch(`/admin/settings/get-zones/${cityId}`)
            .then(res => res.json())
            .then(data => {
                fillSelect(zoneSelect, data, oldZoneId);
                zoneSelect.disabled = false;
            });
    });

    /**
     * 🧩 دالة تعبئة السيلكت مع دعم اختيار القيمة القديمة
     */
    function fillSelect(select, data, oldValue = null) {
        const placeholder = select.getAttribute('data-placeholder') || '-- Select --';
        select.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const name = item.name_ar ?? item.name_en;
            const selected = oldValue && oldValue == item.id ? 'selected' : '';
            select.innerHTML += `<option value="${item.id}" ${selected}>${name}</option>`;
        });
    }
});
</script>
@endsection

@endsection
