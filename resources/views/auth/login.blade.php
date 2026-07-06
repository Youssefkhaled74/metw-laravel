<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Metw</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --metw-orange: #FF7043;
            --metw-purple: #7B00A8;
        }

        * {
            font-family: 'Cairo', system-ui, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #5D008B 0%, #FF7043 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 420px;
            width: 100%;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--metw-purple), var(--metw-orange));
            color: white;
            padding: 2.8rem 2rem 2rem;
            text-align: center;
        }

        .brand-icon {
            font-size: 3.8rem;
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 2.5rem 2.25rem;
        }

        .form-label {
            font-weight: 600;
            color: #2B2430;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: #f8f9fa;
            border: none;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 1rem;
        }

        .form-control:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 112, 67, 0.15);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: none;
            border-radius: 12px;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--metw-orange), #FF5A3C);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(255, 112, 67, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 112, 67, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        
        <!-- Header -->
        <div class="login-header">
            <i class="fas fa-{{ $type === 'admin' ? 'shield-halved' : 'truck-fast' }} brand-icon"></i>
            <h3 class="fw-bold mb-1">تسجيل الدخول</h3>
            <p class="mb-0 opacity-90">Metw - {{ ucfirst($type) }}</p>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger rounded-3 mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route($type . '.login') }}">
                @csrf

                <!-- البريد الإلكتروني -->
                <div class="mb-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <div class="input-group">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="example@domain.com"
                               required autofocus>
                        <span class="input-group-text">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div class="mb-4">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" 
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" 
                               placeholder="••••••••"
                               required>
                        <button type="button" class="input-group-text" onclick="togglePassword()">
                            <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Links -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="#" class="text-muted text-decoration-none small">نسيت كلمة المرور؟</a>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">تذكرني</label>
                    </div>
                </div>

                <!-- Button -->
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    تسجيل الدخول
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>