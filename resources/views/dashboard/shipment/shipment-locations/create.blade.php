@extends('layouts.shipment')

@section('title', __('shipment-dashboard.create_shipment_location'))
@section('page-title', __('shipment-dashboard.create_shipment_location'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shipment.dashboard') }}">@lang('shipment-dashboard.dashboard')</a></li>
    <li class="breadcrumb-item"><a href="{{ route('shipment.locations.index') }}">@lang('shipment-dashboard.locations')</a></li>
    <li class="breadcrumb-item active">@lang('shipment-dashboard.create')</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('shipment-dashboard.location_details')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shipment.shipment-locations.store') }}" method="POST" id="locationForm">
                    @csrf

                    <!-- Country -->
                    <div class="mb-3">
                        <label for="country_id" class="form-label required">@lang('shipment-dashboard.country')</label>
                        <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required>
                            <option value="">@lang('shipment-dashboard.select_country')</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="mb-3">
                        <label for="state_id" class="form-label required">@lang('shipment-dashboard.state')</label>
                        <select class="form-select @error('state_id') is-invalid @enderror" id="state_id" name="state_id" required disabled>
                            <option value="">@lang('shipment-dashboard.select_state')</option>
                        </select>
                        @error('state_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="mb-3">
                        <label for="city_id" class="form-label required">@lang('shipment-dashboard.city')</label>
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required disabled>
                            <option value="">@lang('shipment-dashboard.select_city')</option>
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zone -->
                    <div class="mb-3">
                        <label for="zone_id" class="form-label">@lang('shipment-dashboard.zone_optional')</label>
                        <select class="form-select select2 @error('zone_id') is-invalid @enderror" id="zone_id" name="zone_id[]" multiple disabled>
                            <option value="">@lang('shipment-dashboard.select_zone')</option>
                        </select>
                        @error('zone_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('shipment.locations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back')
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-save"></i> @lang('shipment-dashboard.save_location')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize Select2 for zones
    $('#zone_id').select2({
        placeholder: "{{ __('shipment-dashboard.select_options_placeholder') }}",
        allowClear: true,
        width: '100%'
    });

    // Check if form is valid
    function checkFormValidity() {
        const country = $('#country_id').val();
        const state = $('#state_id').val();
        const city = $('#city_id').val();

        // Enable submit button only if country, state, and city are selected
        if (country && state && city) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }

    // Load states when country changes
    $('#country_id').change(function() {
        const countryId = $(this).val();
        const stateSelect = $('#state_id');
        const citySelect = $('#city_id');
        const zoneSelect = $('#zone_id');

        // Reset dependent fields
        stateSelect.val('').prop('disabled', !countryId);
        citySelect.val('').prop('disabled', true);
        zoneSelect.val('').prop('disabled', true).trigger('change');

        // Clear existing options except the first
        stateSelect.find('option:not(:first)').remove();
        citySelect.find('option:not(:first)').remove();
        zoneSelect.find('option:not(:first)').remove();

        if (countryId) {
            // Show loading
            stateSelect.html('<option value="">Loading...</option>');

            // Fetch states for selected country
            $.ajax({
                url: '{{ route("shipment.locations.get-states", ":countryId") }}'.replace(':countryId', countryId),
                type: 'GET',
                success: function(data) {
                    stateSelect.html('<option value="">@lang("shipment-dashboard.select_state")</option>');
                    $.each(data, function(key, state) {
                        stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
                    });

                    // Enable state select
                    stateSelect.prop('disabled', false);

                    // Check if there's an old value to preserve
                    const oldStateId = "{{ old('state_id') }}";
                    if (oldStateId) {
                        stateSelect.val(oldStateId).trigger('change');
                    }
                },
                error: function() {
                    stateSelect.html('<option value="">Error loading states</option>');
                }
            });
        }

        checkFormValidity();
    });

    // Load cities when state changes
    $('#state_id').change(function() {
        const stateId = $(this).val();
        const citySelect = $('#city_id');
        const zoneSelect = $('#zone_id');

        // Reset dependent fields
        citySelect.val('').prop('disabled', !stateId);
        zoneSelect.val('').prop('disabled', true).trigger('change');

        // Clear existing options except the first
        citySelect.find('option:not(:first)').remove();
        zoneSelect.find('option:not(:first)').remove();

        if (stateId) {
            // Show loading
            citySelect.html('<option value="">Loading...</option>');

            // Fetch cities for selected state
            $.ajax({
                url: '{{ route("shipment.locations.get-cities", ":stateId") }}'.replace(':stateId', stateId),
                type: 'GET',
                success: function(data) {
                    citySelect.html('<option value="">@lang("shipment-dashboard.select_city")</option>');
                    $.each(data, function(key, city) {
                        citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                    });

                    // Enable city select
                    citySelect.prop('disabled', false);

                    // Check if there's an old value to preserve
                    const oldCityId = "{{ old('city_id') }}";
                    if (oldCityId) {
                        citySelect.val(oldCityId).trigger('change');
                    }
                },
                error: function() {
                    citySelect.html('<option value="">Error loading cities</option>');
                }
            });
        }

        checkFormValidity();
    });

    // Load zones when city changes
    $('#city_id').change(function() {
        const cityId = $(this).val();
        const zoneSelect = $('#zone_id');

        // Reset dependent field
        zoneSelect.val('').prop('disabled', !cityId).trigger('change');

        // Clear existing options except the first
        zoneSelect.find('option:not(:first)').remove();

        if (cityId) {
            // Show loading
            zoneSelect.html('<option value="">Loading...</option>');

            // Fetch zones for selected city
            $.ajax({
                url: '{{ route("shipment.locations.get-zones", ":cityId") }}'.replace(':cityId', cityId),
                type: 'GET',
                success: function(data) {
                    zoneSelect.html('<option value=""></option>'); // Empty option for select2
                    $.each(data, function(key, zone) {
                        zoneSelect.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                    });

                    // Enable zone select and reinitialize select2
                    zoneSelect.prop('disabled', false);
                    zoneSelect.select2({
                        placeholder: "{{ __('shipment-dashboard.select_options_placeholder') }}",
                        allowClear: true,
                        width: '100%'
                    });

                    // Check if there's an old value to preserve
                    const oldZoneIds = JSON.parse('{{ json_encode(old("zone_id", [])) }}'.replace(/&quot;/g, '"'));
                    if (oldZoneIds && oldZoneIds.length > 0) {
                        zoneSelect.val(oldZoneIds).trigger('change');
                    }
                },
                error: function() {
                    zoneSelect.html('<option value="">Error loading zones</option>');
                }
            });
        }

        checkFormValidity();
    });

    // Handle form submission
    $('#locationForm').submit(function(e) {
        // Make sure all required fields are filled
        const country = $('#country_id').val();
        const state = $('#state_id').val();
        const city = $('#city_id').val();

        if (!country || !state || !city) {
            e.preventDefault();
            alert('Please select country, state, and city before submitting.');
            return false;
        }
    });

    // Initialize form if there are old values (for validation errors)
    $(document).ready(function() {
        const oldCountryId = "{{ old('country_id') }}";
        const oldStateId = "{{ old('state_id') }}";
        const oldCityId = "{{ old('city_id') }}";

        if (oldCountryId) {
            $('#country_id').val(oldCountryId).trigger('change');
        }

        if (oldStateId && oldCountryId) {
            // State will be loaded by country change event
            setTimeout(function() {
                $('#state_id').val(oldStateId).trigger('change');
            }, 500);
        }

        if (oldCityId && oldStateId) {
            // City will be loaded by state change event
            setTimeout(function() {
                $('#city_id').val(oldCityId).trigger('change');
            }, 1000);
        }

        checkFormValidity();
    });
});
</script>

<style>
    .required:after {
        content: " *";
        color: red;
    }

    select:disabled {
        background-color: #e9ecef;
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endsection

@push('scripts')

@endpush

@push('styles')

@endpush
