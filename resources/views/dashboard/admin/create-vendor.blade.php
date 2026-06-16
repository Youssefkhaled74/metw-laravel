@extends('layouts.admin')

@section('title', __('admin-dashboard.create_vendor'))
@section('page-title', __('admin-dashboard.create_new_vendor'))

@section('page-actions')
    <a href="{{ route('admin.vendors') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_vendors') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.vendor_information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.vendors.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('admin-dashboard.full_name') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('admin-dashboard.email') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('admin-dashboard.phone') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('admin-dashboard.password') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_code" class="form-label">{{ __('admin-dashboard.country_code') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <select class="form-select @error('country_code') is-invalid @enderror"
                                        id="country_code" name="country_code" required>
                                        <option value="">{{ __('admin-dashboard.select') }}</option>
                                        <option value="+1" {{ old('country_code') === '+1' ? 'selected' : '' }}>+1
                                            (US/Canada)</option>
                                        <option value="+44" {{ old('country_code') === '+44' ? 'selected' : '' }}>+44
                                            (UK)</option>
                                        <option value="+966" {{ old('country_code') === '+966' ? 'selected' : '' }}>+966
                                            (Saudi Arabia)</option>
                                        <option value="+971" {{ old('country_code') === '+971' ? 'selected' : '' }}>+971
                                            (UAE)</option>
                                        <option value="+20" {{ old('country_code') === '+20' ? 'selected' : '' }}>+20
                                            (Egypt)</option>
                                    </select>
                                    @error('country_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('admin-dashboard.address') }} <span class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">{{ __('admin-dashboard.latitude') }} <small class="text-muted">(-90 to 90)</small></label>
                                    <input type="number" step="0.000001" min="-90" max="90"
                                        class="form-control @error('latitude') is-invalid @enderror latitude-input" id="latitude"
                                        name="latitude" value="{{ old('latitude') }}" placeholder="e.g., 30.0444">
                                    <div class="invalid-feedback d-none" id="latitudeError">
                                        Latitude must be between -90 and 90
                                    </div>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success d-none" id="latitudeValid">
                                        <i class="fas fa-check-circle"></i> Valid latitude
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">{{ __('admin-dashboard.longitude') }} <small class="text-muted">(-180 to 180)</small></label>
                                    <input type="number" step="0.000001" min="-180" max="180"
                                        class="form-control @error('longitude') is-invalid @enderror longitude-input" id="longitude"
                                        name="longitude" value="{{ old('longitude') }}" placeholder="e.g., 31.2357">
                                    <div class="invalid-feedback d-none" id="longitudeError">
                                        Longitude must be between -180 and 180
                                    </div>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success d-none" id="longitudeValid">
                                        <i class="fas fa-check-circle"></i> Valid longitude
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.vendors') }}" class="btn btn-secondary me-md-2">{{ __('admin-dashboard.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.create_vendor') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const latError = document.getElementById('latitudeError');
        const lonError = document.getElementById('longitudeError');
        const latValid = document.getElementById('latitudeValid');
        const lonValid = document.getElementById('longitudeValid');

        // Email validation elements
        const emailInput = document.getElementById('email');
        const emailError = document.createElement('div');
        emailError.className = 'invalid-feedback';
        emailError.id = 'emailError';
        emailError.textContent = 'Please enter a valid email address';

        const emailValid = document.createElement('small');
        emailValid.className = 'text-success d-none';
        emailValid.id = 'emailValid';
        emailValid.innerHTML = '<i class="fas fa-check-circle"></i> Valid email format';

        // Phone validation elements
        const phoneInput = document.getElementById('phone');
        const phoneError = document.createElement('div');
        phoneError.className = 'invalid-feedback';
        phoneError.id = 'phoneError';
        phoneError.textContent = 'Please enter a valid phone number (minimum 10 digits)';

        const phoneValid = document.createElement('small');
        phoneValid.className = 'text-success d-none';
        phoneValid.id = 'phoneValid';
        phoneValid.innerHTML = '<i class="fas fa-check-circle"></i> Valid phone format';

        // Add validation elements after inputs
        emailInput.parentNode.appendChild(emailError);
        emailInput.parentNode.appendChild(emailValid);
        phoneInput.parentNode.appendChild(phoneError);
        phoneInput.parentNode.appendChild(phoneValid);

        function validateEmail() {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {
                emailInput.classList.remove('is-invalid', 'is-valid');
                emailError.classList.add('d-none');
                emailValid.classList.add('d-none');
                return false;
            }

            if (!emailRegex.test(email)) {
                emailInput.classList.add('is-invalid');
                emailInput.classList.remove('is-valid');
                emailError.classList.remove('d-none');
                emailValid.classList.add('d-none');
                return false;
            } else {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
                emailError.classList.add('d-none');
                emailValid.classList.remove('d-none');
                return true;
            }
        }

        function validatePhone() {
            let phone = phoneInput.value.trim();

            // Remove any non-digit characters for validation
            const digitsOnly = phone.replace(/\D/g, '');

            if (phone === '') {
                phoneInput.classList.remove('is-invalid', 'is-valid');
                phoneError.classList.add('d-none');
                phoneValid.classList.add('d-none');
                return false;
            }

            // Check if it has at least 10 digits
            if (digitsOnly.length < 10) {
                phoneInput.classList.add('is-invalid');
                phoneInput.classList.remove('is-valid');
                phoneError.classList.remove('d-none');
                phoneValid.classList.add('d-none');
                return false;
            } else {
                phoneInput.classList.remove('is-invalid');
                phoneInput.classList.add('is-valid');
                phoneError.classList.add('d-none');
                phoneValid.classList.remove('d-none');
                return true;
            }
        }

        // Optional: Auto-format phone number as user types
        function formatPhoneNumber() {
            let phone = phoneInput.value.replace(/\D/g, '');

            if (phone.length === 0) {
                return;
            }

            // Format based on length
            if (phone.length <= 3) {
                phoneInput.value = phone;
            } else if (phone.length <= 6) {
                phoneInput.value = `(${phone.slice(0, 3)}) ${phone.slice(3)}`;
            } else if (phone.length <= 10) {
                phoneInput.value = `(${phone.slice(0, 3)}) ${phone.slice(3, 6)}-${phone.slice(6)}`;
            } else {
                phoneInput.value = `(${phone.slice(0, 3)}) ${phone.slice(3, 6)}-${phone.slice(6, 10)}`;
            }
        }

        function validateLatitude() {
            const value = parseFloat(latInput.value);

            if (latInput.value === '') {
                latInput.classList.remove('is-invalid', 'is-valid');
                latError.classList.add('d-none');
                latValid.classList.add('d-none');
                return true;
            }

            if (isNaN(value) || value < -90 || value > 90) {
                latInput.classList.add('is-invalid');
                latInput.classList.remove('is-valid');
                latError.classList.remove('d-none');
                latValid.classList.add('d-none');
                return false;
            } else {
                latInput.classList.remove('is-invalid');
                latInput.classList.add('is-valid');
                latError.classList.add('d-none');
                latValid.classList.remove('d-none');
                return true;
            }
        }

        function validateLongitude() {
            const value = parseFloat(lonInput.value);

            if (lonInput.value === '') {
                lonInput.classList.remove('is-invalid', 'is-valid');
                lonError.classList.add('d-none');
                lonValid.classList.add('d-none');
                return true;
            }

            if (isNaN(value) || value < -180 || value > 180) {
                lonInput.classList.add('is-invalid');
                lonInput.classList.remove('is-valid');
                lonError.classList.remove('d-none');
                lonValid.classList.add('d-none');
                return false;
            } else {
                lonInput.classList.remove('is-invalid');
                lonInput.classList.add('is-valid');
                lonError.classList.add('d-none');
                lonValid.classList.remove('d-none');
                return true;
            }
        }

        // Real-time validation
        emailInput.addEventListener('input', validateEmail);
        emailInput.addEventListener('blur', validateEmail);

        phoneInput.addEventListener('input', function() {
            // Optional: Uncomment the line below if you want auto-formatting
            // formatPhoneNumber();
            validatePhone();
        });
        phoneInput.addEventListener('blur', validatePhone);

        latInput.addEventListener('input', validateLatitude);
        latInput.addEventListener('change', validateLatitude);
        lonInput.addEventListener('input', validateLongitude);
        lonInput.addEventListener('change', validateLongitude);

        // Validate on form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const isEmailValid = validateEmail();
            const isPhoneValid = validatePhone();
            const isLatValid = validateLatitude();
            const isLonValid = validateLongitude();

            if (!isEmailValid || !isPhoneValid || !isLatValid || !isLonValid) {
                e.preventDefault();

                // Scroll to the first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        // Validate on page load if values exist
        if (emailInput.value) validateEmail();
        if (phoneInput.value) validatePhone();
        if (latInput.value) validateLatitude();
        if (lonInput.value) validateLongitude();
    });
</script>
@endsection
