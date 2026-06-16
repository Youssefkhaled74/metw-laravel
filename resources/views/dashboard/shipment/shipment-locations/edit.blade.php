@extends('layouts.shipment')

@section('title', __('shipment-dashboard.edit_shipment_location'))
@section('page-title', __('shipment-dashboard.edit_shipment_location'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shipment.dashboard') }}">@lang('shipment-dashboard.dashboard')</a></li>
    <li class="breadcrumb-item"><a href="{{ route('shipment.shipment-locations.index') }}">@lang('shipment-dashboard.shipment_locations')</a></li>
    <li class="breadcrumb-item active">@lang('shipment-dashboard.edit')</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('shipment-dashboard.edit_location')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shipment.shipment-locations.update', $shipmentLocation->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- Country (Multiple Selection) -->
                    <div class="mb-3">
                        <label for="country_id" class="form-label required">@lang('shipment-dashboard.country')</label>
                        <select class="form-select select2 @error('country') is-invalid @enderror"
                                id="country_id"
                                name="country[]" multiple required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ in_array($country->id, old('country', $shipmentLocation->country ?? [])) ? 'selected' : '' }}>
                                    {{ $country->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- State (Multiple Selection) -->
                    <div class="mb-3">
                        <label for="state_id" class="form-label required">@lang('shipment-dashboard.state')</label>
                        <select class="form-select select2 @error('state') is-invalid @enderror"
                                id="state_id"
                                name="state[]" multiple required>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}"
                                    {{ in_array($state->id, old('state', $shipmentLocation->state ?? [])) ? 'selected' : '' }}>
                                    {{ $state->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City (Multiple Selection) -->
                    <div class="mb-3">
                        <label for="city_id" class="form-label required">@lang('shipment-dashboard.city')</label>
                        <select class="form-select select2 @error('city') is-invalid @enderror"
                                id="city_id"
                                name="city[]" multiple required>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ in_array($city->id, old('city', $shipmentLocation->city ?? [])) ? 'selected' : '' }}>
                                    {{ $city->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zone (Multiple Selection) -->
                    <div class="mb-3">
                        <label for="zone_id" class="form-label">@lang('shipment-dashboard.zone')</label>
                        <select class="form-select select2 @error('zone') is-invalid @enderror"
                                id="zone_id"
                                name="zone[]" multiple>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ in_array($zone->id, old('zone', $shipmentLocation->zone ?? [])) ? 'selected' : '' }}>
                                    {{ $zone->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('shipment.shipment-locations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back')
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('shipment-dashboard.update_location')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "{{ __('shipment-dashboard.select_options_placeholder') }}",
        allowClear: true,
        width: '100%'
    });

    // When countries are selected, load related states
    $('#country_id').on('change', function() {
        var countryIds = $(this).val();

        if (countryIds && countryIds.length > 0) {
            $.ajax({
                url: "{{ route('shipment.get-states-by-countries') }}",
                type: 'GET',
                data: {
                    country_ids: countryIds
                },
                success: function(data) {
                    $('#state_id').empty();
                    $('#city_id').empty(); // Clear cities when states change

                    // Add new states
                    $.each(data.states, function(key, state) {
                        $('#state_id').append('<option value="' + state.id + '">' + state.name_en + '</option>');
                    });

                    // Reinitialize Select2
                    $('#state_id').trigger('change.select2');
                }
            });
        } else {
            $('#state_id').empty().trigger('change.select2');
            $('#city_id').empty().trigger('change.select2');
        }
    });

    // When states are selected, load related cities
    $('#state_id').on('change', function() {
        var stateIds = $(this).val();

        if (stateIds && stateIds.length > 0) {
            $.ajax({
                url: "{{ route('shipment.get-cities-by-states') }}",
                type: 'GET',
                data: {
                    state_ids: stateIds
                },
                success: function(data) {
                    $('#city_id').empty();

                    // Add new cities
                    $.each(data.cities, function(key, city) {
                        $('#city_id').append('<option value="' + city.id + '">' + city.name_en + '</option>');
                    });

                    // Reinitialize Select2
                    $('#city_id').trigger('change.select2');
                }
            });
        } else {
            $('#city_id').empty().trigger('change.select2');
        }
    });
});
</script>
@endsection
