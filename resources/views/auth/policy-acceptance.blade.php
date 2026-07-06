<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموافقة على الشروط والسياسات - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ff8a00 0%, #764ba2 100%);
        }
        .accept-card {
            max-width: 980px;
            margin: 40px auto;
            border: 0;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,.2);
        }
        .accept-header {
            background: #1f2340;
            color: #fff;
            padding: 28px;
        }
        .policy-box {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 22px;
            padding: 20px;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <div class="card accept-card">
            <div class="accept-header">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div>
                        <h1 class="h3 mb-2">الموافقة على الشروط والسياسات</h1>
                        <p class="mb-0">قبل الدخول إلى لوحة التحكم الخاصة بـ {{ $accountLabel }}، يرجى مراجعة الشروط والسياسات والموافقة عليها.</p>
                    </div>
                    <form method="POST" action="{{ route($guard . '.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-4 p-lg-5">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="policy-box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">الشروط والأحكام</h2>
                                <a href="{{ route('website.terms') }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض الصفحة</a>
                            </div>
                            <p class="text-muted">{{ $terms?->translated_content ? strip_tags($terms->translated_content) : 'يمكنك مراجعة الشروط والأحكام العامة من صفحة الشروط.' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="policy-box">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">سياسة الاستخدام والخصوصية</h2>
                                <a href="{{ route('website.policies') }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض الصفحة</a>
                            </div>
                            <p class="text-muted">{{ $policy?->translated_content ? strip_tags($policy->translated_content) : 'يمكنك مراجعة السياسات العامة من صفحة السياسات.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    @if($policyVersion)
                        <strong>إصدار السياسة الحالي:</strong> {{ $policyVersion }}
                    @else
                        <strong>إصدار السياسة الحالي:</strong> غير متوفر
                    @endif
                </div>

                <form method="POST" action="{{ route($guard . '.policy-acceptance.accept') }}">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('acceptance') is-invalid @enderror" type="checkbox" name="acceptance" value="1" id="acceptance" required>
                        <label class="form-check-label" for="acceptance">
                            أوافق على الشروط والأحكام وسياسة الاستخدام والخصوصية الخاصة بالموقع.
                        </label>
                        @error('acceptance')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <a href="{{ route('website.home') }}" class="btn btn-outline-secondary">العودة إلى الموقع</a>
                        <button type="submit" class="btn btn-primary">حفظ الموافقة والدخول</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
