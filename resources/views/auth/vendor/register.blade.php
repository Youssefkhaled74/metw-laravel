<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Registration - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            padding: 2rem 0;
            border-bottom: 1px solid #eee;
        }

        .auth-header i {
            font-size: 3rem;
            color: #764ba2;
            margin-bottom: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card auth-card">
                    <div class="auth-header">
                        <i class="fas fa-user-plus"></i>
                        <h4 class="mb-0">Vendor Registration</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('vendor.register') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Owner & Brand -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Owner Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           class="form-control @error('name') is-invalid @enderror">
                                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <!-- Email, Phone, Country Code -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="country_code" class="form-label">Country Code</label>
                                    <input type="text" id="country_code" name="country_code" value="{{ old('country_code') }}" required
                                           placeholder="+20"
                                           class="form-control @error('country_code') is-invalid @enderror">
                                    @error('country_code')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                                           class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                           class="form-control @error('email') is-invalid @enderror">
                                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" name="address" rows="3" required
                                          class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <!-- Logo -->
                            <div class="mb-3">
                                <label for="logo" class="form-label">Store Logo</label>
                                <input type="file" id="logo" name="logo" accept="image/*"
                                       class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <!-- Latitude & Longitude -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Latitude (optional)</label>
                                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                                           class="form-control @error('latitude') is-invalid @enderror">
                                    @error('latitude')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Longitude (optional)</label>
                                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                                           class="form-control @error('longitude') is-invalid @enderror">
                                    @error('longitude')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" required
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                           class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0 text-muted">
                                Already have an account?
                                <a href="{{ route('vendor.login') }}" class="text-decoration-none text-primary">Login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
