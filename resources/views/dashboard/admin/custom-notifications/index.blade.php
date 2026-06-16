@extends('layouts.admin')

@section('title', __('admin-dashboard.custom_notifications'))
@section('page-title', __('admin-dashboard.custom_notifications'))

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12">
            <div class="card notification-card shadow-sm">
                <div class="card-header notification-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="notification-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-semibold">{{ __('admin-dashboard.send_notification') }}</h5>
                            <p class="mb-0 text-muted small">
                                {{ __('admin-dashboard.body_hint') }}
                            </p>
                        </div>
                    </div>
                    <span class="badge bg-light text-primary fw-semibold px-3 py-2">
                        <i class="fas fa-broadcast-tower me-1"></i>
                        {{ __('admin-dashboard.custom_notifications') }}
                    </span>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.custom-notifications.send') }}" id="notificationForm">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                    {{ __('admin-dashboard.title') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm modern-input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-heading text-muted"></i>
                                    </span>
                                    <input type="text"
                                           name="title"
                                           class="form-control modern-input @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}"
                                           required
                                           placeholder="{{ __('admin-dashboard.title_placeholder') }}">
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                    {{ app()->getLocale() === 'ar' ? 'نوع المستلمين' : 'Recipient Type' }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm modern-input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-user-tag text-muted"></i>
                                    </span>
                                    <select name="recipient_type"
                                            id="recipientType"
                                            class="form-select modern-input @error('recipient_type') is-invalid @enderror"
                                            required>
                                        <option value="user" {{ old('recipient_type', 'user') === 'user' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'المستخدمين' : 'Users' }}</option>
                                        <option value="vendor" {{ old('recipient_type') === 'vendor' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'البائعين' : 'Vendors' }}</option>
                                        <option value="shipment_company" {{ old('recipient_type') === 'shipment_company' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'شركات الشحن' : 'Shipment Companies' }}</option>
                                    </select>
                                    @error('recipient_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-uppercase text-muted">
                                    {{ __('admin-dashboard.target') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm modern-input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-users text-muted"></i>
                                    </span>
                                    <select name="target"
                                            id="target"
                                            class="form-select modern-input @error('target') is-invalid @enderror"
                                            required>
                                        <option value="all" {{ old('target', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</option>
                                        <option value="one" {{ old('target') === 'one' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'فردي' : 'One' }}</option>
                                        <option value="multiple" {{ old('target') === 'multiple' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متعدد' : 'Multiple' }}</option>
                                    </select>
                                    @error('target')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                {{ __('admin-dashboard.notification_type') }} <span class="text-danger">*</span>
                            </label>

                            <select name="app_type"
                                    class="form-select modern-input"
                                    required>
                                <option value="ecommerce">{{ __('admin-dashboard.ecommerce') }}</option>
                                <option value="shipment">{{ __('admin-dashboard.shipment') }}</option>
                                <option value="both">{{ __('admin-dashboard.both') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">
                                {{ __('admin-dashboard.message_body') }} <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <textarea name="body"
                                          class="form-control modern-textarea @error('body') is-invalid @enderror"
                                          rows="5"
                                          required
                                          placeholder="{{ __('admin-dashboard.body_placeholder') }}">{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">
                                        {{ __('admin-dashboard.body_hint') }}
                                    </small>
                                    <small class="text-muted">
                                        <span id="charCount">0</span> / 1000
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Single User Selection -->
                        <div class="mb-3 d-none" id="one-user-section">
                            <label class="form-label fw-semibold small text-uppercase text-muted" id="singleRecipientLabel">
                                {{ __('admin-dashboard.select_user') }}
                            </label>
                            <div class="input-group input-group-sm modern-input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <select name="user_id"
                                        class="form-select modern-input @error('user_id') is-invalid @enderror"
                                        id="singleUserSelect">
                                    <option value="">{{ __('admin-dashboard.choose_user') }}</option>
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                {{ __('admin-dashboard.single_user_hint') }}
                            </small>
                        </div>

                        <!-- Multiple Users Selection - Enhanced -->
                        <div class="mb-3 d-none" id="multi-user-section">
                            <label class="form-label fw-semibold small text-uppercase text-muted" id="multiRecipientLabel">
                                {{ __('admin-dashboard.select_users') }}
                            </label>

                            <!-- Search Box -->
                            <div class="input-group input-group-sm modern-input-group mb-3">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text"
                                       id="userSearch"
                                       class="form-control modern-input"
                                       placeholder="{{ __('admin-dashboard.search_users') }}">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Selection Stats -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted" id="selectedCount">
                                    0 {{ __('admin-dashboard.users_selected') }}
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary" id="selectAll">
                                        {{ __('admin-dashboard.select_all') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="deselectAll">
                                        {{ __('admin-dashboard.deselect_all') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Users List with Checkboxes -->
                            <div class="card border-0 user-list-card">
                                <div class="card-body p-2">
                                    <div id="usersList"></div>
                                </div>
                            </div>

                            <!-- Hidden select for form submission (optional, depends on your backend) -->
                            <select name="users[]" class="d-none" id="selectedUsers" multiple></select>

                            @error('users')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                {{ __('admin-dashboard.multi_select_hint') }}
                            </small>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2 notification-actions">
                            <button type="submit" class="btn btn-primary btn-sm" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> {{ __('admin-dashboard.send_notification') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="previewBtn">
                                <i class="fas fa-eye me-1"></i> {{ __('admin-dashboard.preview') }}
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-times me-1"></i> {{ __('admin-dashboard.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold">{{ __('admin-dashboard.preview_notification') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <h6 id="previewTitle" class="mb-1"></h6>
                            <small class="text-muted" id="previewDate"></small>
                        </div>
                        <div class="card-body">
                            <p id="previewBody" class="mb-0"></p>
                        </div>
                        <div class="card-footer bg-light text-muted small border-0" id="previewRecipients"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('admin-dashboard.close') }}</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('notificationForm').submit()">
                        {{ __('admin-dashboard.send_now') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .notification-card {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }

    .notification-header {
        background: linear-gradient(135deg, #f5f7ff, #eef3ff);
        border-bottom: 1px solid #e0e7ff;
        padding: 1.25rem 1.5rem;
    }

    .notification-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        font-size: 1.2rem;
    }

    .modern-input-group .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1px solid #e0e0e0;
        border-right: 0;
    }

    .modern-input-group .form-control,
    .modern-input-group .form-select {
        border-radius: 0 10px 10px 0;
        border: 1px solid #e0e0e0;
        border-left: 0;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        background-color: #fafafa;
    }

    .modern-input-group .form-control:focus,
    .modern-input-group .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
        background-color: #fff;
    }

    .modern-textarea {
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 0.75rem 0.9rem;
        font-size: 0.9rem;
        background-color: #fafafa;
        resize: vertical;
        min-height: 140px;
    }

    .modern-textarea:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
        background-color: #fff;
        outline: none;
    }

    .notification-actions .btn-sm {
        font-size: 0.8rem;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-actions .btn-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
    }

    .notification-actions .btn-primary:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
    }

    .notification-actions .btn-light {
        border: 1px solid #e5e7eb;
    }

    .user-list-card {
        max-height: 300px;
        overflow-y: auto;
        border-radius: 12px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
    }

    .user-item {
        padding: 8px 10px;
        border-radius: 6px;
        transition: background-color 0.2s, transform 0.15s;
    }

    .user-item:hover {
        background-color: #eef2ff;
        transform: translateX(2px);
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
        font-weight: 600;
    }

    .form-check-label {
        cursor: pointer;
        width: 100%;
    }

    .user-item.highlight {
        background-color: #e7f1ff;
        border-left: 3px solid #0d6efd;
    }

    #usersList::-webkit-scrollbar {
        width: 6px;
    }

    #usersList::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #usersList::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 3px;
    }

    #usersList::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>
@endsection

@php
    $customNotificationRecipientData = [
        'user' => $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->username,
                'email' => $user->email,
            ];
        })->values()->all(),
        'vendor' => $vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
            ];
        })->values()->all(),
        'shipment_company' => $shipmentCompanies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
            ];
        })->values()->all(),
    ];

    $customNotificationOldRecipientType = old('recipient_type', 'user');
    $customNotificationOldTarget = old('target', 'all');
    $customNotificationOldSingleId = old('user_id');
    $customNotificationOldMultipleIds = old('users', []);
@endphp

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recipientTypeSelect = document.getElementById('recipientType');
            const targetSelect = document.getElementById('target');
            const oneUserSection = document.getElementById('one-user-section');
            const multiUserSection = document.getElementById('multi-user-section');
            const singleRecipientLabel = document.getElementById('singleRecipientLabel');
            const multiRecipientLabel = document.getElementById('multiRecipientLabel');
            const userSearch = document.getElementById('userSearch');
            const clearSearch = document.getElementById('clearSearch');
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const selectedCount = document.getElementById('selectedCount');
            const usersList = document.getElementById('usersList');
            const singleUserSelect = document.getElementById('singleUserSelect');
            const bodyTextarea = document.querySelector('textarea[name="body"]');
            const charCount = document.getElementById('charCount');
            const previewBtn = document.getElementById('previewBtn');
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            const submitBtn = document.getElementById('submitBtn');

            const locale = document.documentElement.lang === 'ar' ? 'ar' : 'en';
            const oldRecipientType = @json($customNotificationOldRecipientType);
            const oldTarget = @json($customNotificationOldTarget);
            const oldSingleId = @json($customNotificationOldSingleId);
            const oldMultipleIds = @json($customNotificationOldMultipleIds);

            const recipientsData = @json($customNotificationRecipientData);

            const recipientLabels = {
                ar: {
                    user: { icon: 'fa-user', singular: 'مستخدم', plural: 'المستخدمين', all: 'جميع المستخدمين', one: 'مستخدم واحد', multiple: 'مستخدمين متعددين', choose: 'اختر مستخدم...', search: 'البحث عن المستخدمين...' },
                    vendor: { icon: 'fa-store', singular: 'بائع', plural: 'البائعين', all: 'جميع البائعين', one: 'بائع واحد', multiple: 'بائعين متعددين', choose: 'اختر بائع...', search: 'البحث عن البائعين...' },
                    shipment_company: { icon: 'fa-truck', singular: 'شركة شحن', plural: 'شركات الشحن', all: 'جميع شركات الشحن', one: 'شركة شحن واحدة', multiple: 'شركات شحن متعددة', choose: 'اختر شركة شحن...', search: 'البحث عن شركات الشحن...' }
                },
                en: {
                    user: { icon: 'fa-user', singular: 'User', plural: 'Users', all: 'All Users', one: 'One User', multiple: 'Multiple Users', choose: 'Choose a user...', search: 'Search users...' },
                    vendor: { icon: 'fa-store', singular: 'Vendor', plural: 'Vendors', all: 'All Vendors', one: 'One Vendor', multiple: 'Multiple Vendors', choose: 'Choose a vendor...', search: 'Search vendors...' },
                    shipment_company: { icon: 'fa-truck', singular: 'Shipment Company', plural: 'Shipment Companies', all: 'All Shipment Companies', one: 'One Shipment Company', multiple: 'Multiple Shipment Companies', choose: 'Choose a shipment company...', search: 'Search shipment companies...' }
                }
            };

            function currentType() {
                return recipientTypeSelect.value || 'user';
            }

            function currentLabelSet() {
                return recipientLabels[locale][currentType()];
            }

            function getCurrentRecipients() {
                return recipientsData[currentType()] || [];
            }

            function updateTargetOptionsText() {
                const labels = currentLabelSet();
                targetSelect.querySelector('option[value="all"]').textContent = labels.all;
                targetSelect.querySelector('option[value="one"]').textContent = labels.one;
                targetSelect.querySelector('option[value="multiple"]').textContent = labels.multiple;
            }

            function updateSelectionSectionTexts() {
                const labels = currentLabelSet();
                singleRecipientLabel.textContent = locale === 'ar' ? `اختر ${labels.singular}` : `Select ${labels.singular}`;
                multiRecipientLabel.textContent = locale === 'ar' ? `اختر ${labels.plural}` : `Select ${labels.plural}`;
                userSearch.placeholder = labels.search;
            }

            function renderSingleSelect(selectedId = null) {
                const labels = currentLabelSet();
                const recipients = getCurrentRecipients();

                singleUserSelect.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = labels.choose;
                singleUserSelect.appendChild(defaultOption);

                recipients.forEach(recipient => {
                    const option = document.createElement('option');
                    option.value = recipient.id;
                    option.textContent = `${recipient.name} (${recipient.email || 'N/A'})`;
                    if (String(selectedId || '') === String(recipient.id)) {
                        option.selected = true;
                    }
                    singleUserSelect.appendChild(option);
                });
            }

            function renderMultipleList(selectedIds = []) {
                const recipients = getCurrentRecipients();
                const selectedSet = new Set((selectedIds || []).map(String));

                usersList.innerHTML = '';

                recipients.forEach(recipient => {
                    const item = document.createElement('div');
                    item.className = 'user-item mb-2';
                    item.dataset.userId = recipient.id;
                    item.dataset.username = String(recipient.name || '').toLowerCase();
                    item.dataset.email = String(recipient.email || '').toLowerCase();

                    const initials = String(recipient.name || '?').trim().charAt(0).toUpperCase();
                    const checkboxId = `recipient_${currentType()}_${recipient.id}`;
                    const checkedAttr = selectedSet.has(String(recipient.id)) ? 'checked' : '';

                    item.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input user-checkbox" type="checkbox" name="users[]" value="${recipient.id}" id="${checkboxId}" ${checkedAttr}>
                            <label class="form-check-label d-flex align-items-center" for="${checkboxId}">
                                <div class="avatar-sm me-2 bg-primary rounded-circle d-flex align-items-center justify-content-center text-white">${initials}</div>
                                <div>
                                    <div class="fw-semibold">${recipient.name || 'N/A'}</div>
                                    <div class="small text-muted">${recipient.email || 'N/A'}</div>
                                </div>
                            </label>
                        </div>
                    `;

                    usersList.appendChild(item);
                });
            }

            // Toggle user sections based on target selection
            function toggleUserSections() {
                oneUserSection.classList.add('d-none');
                multiUserSection.classList.add('d-none');

                const selectedTarget = targetSelect.value;
                if (selectedTarget === 'one') {
                    oneUserSection.classList.remove('d-none');
                    singleUserSelect.required = true;
                } else if (selectedTarget === 'multiple') {
                    multiUserSection.classList.remove('d-none');
                } else {
                    singleUserSelect.required = false;
                }

                updateSelectedCount();
            }

            // Update selected users count
            function updateSelectedCount() {
                const selected = usersList.querySelectorAll('.user-checkbox:checked');
                const labels = currentLabelSet();
                selectedCount.textContent = locale === 'ar'
                    ? `${selected.length} ${labels.singular} محدد`
                    : `${selected.length} ${labels.singular} selected`;

                // Update hidden select for form submission
                const hiddenSelect = document.getElementById('selectedUsers');
                hiddenSelect.innerHTML = '';
                selected.forEach(checkbox => {
                    const option = document.createElement('option');
                    option.value = checkbox.value;
                    option.selected = true;
                    hiddenSelect.appendChild(option);
                });
            }

            // Search users in multi-select
            function searchUsers() {
                const searchTerm = userSearch.value.toLowerCase();
                const userItems = usersList.querySelectorAll('.user-item');

                userItems.forEach(item => {
                    const username = item.dataset.username;
                    const email = item.dataset.email;

                    if (username.includes(searchTerm) || email.includes(searchTerm)) {
                        item.style.display = 'block';
                        item.classList.add('highlight');
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('highlight');
                    }
                });
            }

            // Select/Deselect all users
            selectAllBtn.addEventListener('click', function() {
                usersList.querySelectorAll('.user-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            });

            deselectAllBtn.addEventListener('click', function() {
                usersList.querySelectorAll('.user-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            });

            // Character count for message body
            function updateCharCount() {
                const length = bodyTextarea.value.length;
                charCount.textContent = length;
                charCount.className = length > 1000 ? 'text-danger' : 'text-muted';
            }

            // Preview notification
            previewBtn.addEventListener('click', function() {
                const title = document.querySelector('input[name="title"]').value;
                const body = bodyTextarea.value;
                const target = targetSelect.value;
                const labels = currentLabelSet();

                document.getElementById('previewTitle').textContent = title || '{{ __('admin-dashboard.no_title') }}';
                document.getElementById('previewBody').textContent = body || '{{ __('admin-dashboard.no_message') }}';
                document.getElementById('previewDate').textContent = new Date().toLocaleString();

                let recipientsText = '';
                if (target === 'all') {
                    recipientsText = labels.all;
                } else if (target === 'one') {
                    const selectedOption = singleUserSelect.options[singleUserSelect.selectedIndex];
                    recipientsText = selectedOption?.text || '{{ __('admin-dashboard.no_user_selected') }}';
                } else if (target === 'multiple') {
                    const selected = usersList.querySelectorAll('.user-checkbox:checked');
                    recipientsText = locale === 'ar'
                        ? `${selected.length} ${labels.plural}`
                        : `${selected.length} ${labels.plural}`;
                }

                document.getElementById('previewRecipients').textContent =
                    `{{ __('admin-dashboard.recipients') }}: ${recipientsText}`;

                previewModal.show();
            });

            // Form validation and submission
            document.getElementById('notificationForm').addEventListener('submit', function(e) {
                const target = targetSelect.value;

                if (target === 'one' && !singleUserSelect.value) {
                    e.preventDefault();
                    singleUserSelect.focus();
                    showToast('{{ __('admin-dashboard.please_select_user') }}', 'warning');
                } else if (target === 'multiple') {
                    const selected = usersList.querySelectorAll('.user-checkbox:checked');
                    if (selected.length === 0) {
                        e.preventDefault();
                        showToast('{{ __('admin-dashboard.please_select_at_least_one_user') }}', 'warning');
                    }
                }

                // Show loading state
                if (!e.defaultPrevented) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __('admin-dashboard.sending') }}';
                    submitBtn.disabled = true;
                }
            });

            // Helper function for toast notifications
            function showToast(message, type = 'info') {
                // You can implement a toast notification system here
                alert(message); // Simple alert for now
            }

            // Event Listeners
            recipientTypeSelect.addEventListener('change', function() {
                updateTargetOptionsText();
                updateSelectionSectionTexts();
                renderSingleSelect(null);
                renderMultipleList([]);
                toggleUserSections();
                searchUsers();
            });

            targetSelect.addEventListener('change', toggleUserSections);
            userSearch.addEventListener('input', searchUsers);
            clearSearch.addEventListener('click', function() {
                userSearch.value = '';
                searchUsers();
            });
            usersList.addEventListener('change', function(event) {
                if (event.target.classList.contains('user-checkbox')) {
                    updateSelectedCount();
                }
            });
            bodyTextarea.addEventListener('input', updateCharCount);

            // Initialize
            recipientTypeSelect.value = oldRecipientType || 'user';
            updateTargetOptionsText();
            updateSelectionSectionTexts();
            renderSingleSelect(oldSingleId || null);
            renderMultipleList(oldMultipleIds || []);
            targetSelect.value = oldTarget || 'all';
            toggleUserSections();
            updateCharCount();
            updateSelectedCount();
            searchUsers();
        });
    </script>
@endsection
