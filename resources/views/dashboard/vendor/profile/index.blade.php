@extends('layouts.vendor')

@section('title', __('vendor-dashboard.profile'))
@section('page-title', __('vendor-dashboard.my_profile'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('vendor-dashboard.profile_information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('vendor-dashboard.name') }}</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $vendor->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('vendor-dashboard.email_address') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $vendor->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('vendor-dashboard.phone_number') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">{{ __('vendor-dashboard.store_logo') }}</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                        id="logo" name="logo" accept="image/*">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($vendor->logo)
                                        <div class="mt-2">
                                            <img src="{{ asset( $vendor->logo) }}" alt="Store Logo" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('vendor-dashboard.address') }}</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('vendor-dashboard.update_profile') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Business Information -->
            @if($vendor->businessProfile)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>المعلومات التجارية</h5>
                    <span class="badge bg-{{ $vendor->businessProfile->status->value === 'approved' ? 'success' : ($vendor->businessProfile->status->value === 'rejected' ? 'danger' : 'warning') }}">
                        {{ match($vendor->businessProfile->status->value) {
                            'approved' => 'معتمدة',
                            'rejected' => 'مرفوضة',
                            'pending_review' => 'قيد المراجعة',
                            default => $vendor->businessProfile->status->value,
                        } }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الاسم القانوني</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->legal_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الاسم التجاري</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->commercial_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الرقم الضريبي</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->tax_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">رقم السجل التجاري</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->commercial_register_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">جهة الاتصال</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->contact_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">هاتف الاتصال</small>
                            <div class="fw-semibold">{{ $vendor->businessProfile->contact_phone ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Account Profile Information -->
            @if($vendor->accountProfile)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2 text-info"></i>معلومات الحساب</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($vendor->accountProfile->national_id)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الرقم القومي</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->national_id }}</div>
                        </div>
                        @endif
                        @if($vendor->accountProfile->gender)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الجنس</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->gender === 'male' ? 'ذكر' : 'أنثى' }}</div>
                        </div>
                        @endif
                        @if($vendor->accountProfile->date_of_birth)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">تاريخ الميلاد</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->date_of_birth->format('Y-m-d') }}</div>
                        </div>
                        @endif
                        @if($vendor->accountProfile->alternate_phone)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">هاتف بديل</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->alternate_phone }}</div>
                        </div>
                        @endif
                        @if($vendor->accountProfile->display_name)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">الاسم المعروض</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->display_name }}</div>
                        </div>
                        @endif
                        @if($vendor->accountProfile->account_number)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">رقم الحساب</small>
                            <div class="fw-semibold">{{ $vendor->accountProfile->account_number }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Uploaded Documents -->
            @if($vendor->mediaFiles->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2 text-success"></i>المستندات المرفقة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($vendor->mediaFiles as $file)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center gap-3 p-3 border rounded">
                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    @if(in_array($file->extension, ['jpg', 'jpeg', 'png']))
                                        <i class="fas fa-image text-primary"></i>
                                    @elseif($file->extension === 'pdf')
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    @else
                                        <i class="fas fa-file text-secondary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $file->original_name }}</div>
                                    <small class="text-muted">{{ strtoupper($file->extension) }} · {{ round($file->size / 1024, 1) }} KB</small>
                                </div>
                                <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Change Password -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('vendor-dashboard.change_password') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.change-password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('vendor-dashboard.current_password') }}</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('vendor-dashboard.new_password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('vendor-dashboard.confirm_new_password') }}</label>
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> {{ __('vendor-dashboard.change_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Profile Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($vendor->logo)
                            <img src="{{ asset($vendor->logo) }}" alt="Store Logo"
                                class="img-fluid rounded-circle mb-3" style="max-width: 150px">
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 150px; height: 150px">
                                <i class="fas fa-store fa-4x text-muted"></i>
                            </div>
                        @endif
                        <h4 class="mb-0">{{ $vendor->name }}</h4>
                        <p class="text-muted">{{ __('vendor-dashboard.vendor') }}</p>
                        @if($vendor->brand_name)
                            <p class="text-muted small mb-0">{{ $vendor->brand_name }}</p>
                        @endif
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.email_address') }}</small>
                            <div>{{ $vendor->email }}</div>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.phone_number') }}</small>
                            <div>{{ $vendor->phone }}</div>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.address') }}</small>
                            <div>{{ $vendor->address }}</div>
                        </div>
                        @if($vendor->vendor_number)
                        <div class="list-group-item">
                            <small class="text-muted d-block">رقم البائع</small>
                            <div class="fw-semibold">{{ $vendor->vendor_number }}</div>
                        </div>
                        @endif
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.member_since') }}</small>
                            <div>{{ $vendor->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Profile Status -->
            @if($vendor->businessProfile)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">الملف التجاري</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px;"
                             @if($vendor->businessProfile->status->value === 'approved')
                                class="bg-success-subtle text-success"
                             @elseif($vendor->businessProfile->status->value === 'rejected')
                                class="bg-danger-subtle text-danger"
                             @else
                                class="bg-warning-subtle text-warning"
                             @endif>
                            <i class="fas fa-{{ $vendor->businessProfile->status->value === 'approved' ? 'check-circle' : ($vendor->businessProfile->status->value === 'rejected' ? 'times-circle' : 'clock') }}"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">
                                {{ match($vendor->businessProfile->status->value) {
                                    'approved' => 'تم الاعتماد',
                                    'rejected' => 'مرفوض',
                                    'pending_review' => 'قيد المراجعة',
                                    default => $vendor->businessProfile->status->value,
                                } }}
                            </div>
                            @if($vendor->businessProfile->submitted_at)
                                <small class="text-muted">أُرسل {{ $vendor->businessProfile->submitted_at->diffForHumans() }}</small>
                            @endif
                        </div>
                    </div>
                    @if($vendor->businessProfile->rejection_reason)
                        <div class="alert alert-danger small mb-0">
                            <strong>سبب الرفض:</strong> {{ $vendor->businessProfile->rejection_reason }}
                        </div>
                    @endif
                    <a href="{{ route('vendor.business-profile.upsert') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="fas fa-edit me-1"></i> تعديل الملف التجاري
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
