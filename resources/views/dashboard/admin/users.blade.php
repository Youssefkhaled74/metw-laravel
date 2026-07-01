@extends('layouts.admin')

@section('title', __('admin-dashboard.users_management'))
@section('page-title', __('admin-dashboard.users_management'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $users->count();
    $totalCount = method_exists($users, 'total') ? $users->total() : $users->count();

    $emailVerifiedCount = $users->filter(fn ($user) => ! empty($user->email_verified_at))->count();
    $phoneVerifiedCount = $users->filter(fn ($user) => ! empty($user->phone_verified_at))->count();

    $sortBy = request('sort_by', 'created_at');
    $sortDir = request('sort_dir', 'desc');

    $sortUrl = static function (string $column) use ($sortBy, $sortDir) {
        $nextDir = $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';

        return route('admin.users', array_merge(
            request()->except('page'),
            [
                'sort_by' => $column,
                'sort_dir' => $nextDir,
            ]
        ));
    };

    $sortIcon = static function (string $column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) {
            return 'fa-sort';
        }

        return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };

    $quickSorts = [
        [
            'label' => $text('Newest', 'الأحدث'),
            'params' => ['sort_by' => 'created_at', 'sort_dir' => 'desc'],
            'active' => $sortBy === 'created_at' && $sortDir === 'desc',
        ],
        [
            'label' => $text('Oldest', 'الأقدم'),
            'params' => ['sort_by' => 'created_at', 'sort_dir' => 'asc'],
            'active' => $sortBy === 'created_at' && $sortDir === 'asc',
        ],
        [
            'label' => $text('Name A-Z', 'الاسم أ-ي'),
            'params' => ['sort_by' => 'username', 'sort_dir' => 'asc'],
            'active' => $sortBy === 'username' && $sortDir === 'asc',
        ],
        [
            'label' => $text('User number', 'رقم المستخدم'),
            'params' => ['sort_by' => 'user_number', 'sort_dir' => 'desc'],
            'active' => $sortBy === 'user_number',
        ],
    ];

    $baseUsersUrl = route('admin.users');
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.users') }}</li>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="usr-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="usr-hero-card">
            <div class="usr-hero-main">
                <div class="usr-hero-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div class="usr-hero-text">
                    <span class="usr-chip">
                        {{ $text('Users management', 'إدارة المستخدمين') }}
                    </span>

                    <h4>{{ __('admin-dashboard.users_management') }}</h4>

                    <p>
                        {{ $text(
                            'Search, review, sort, open user profiles, and adjust wallets from one focused screen.',
                            'ابحث وراجع ورتب وافتح ملفات المستخدمين وعدّل المحافظ من شاشة واحدة واضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="usr-metrics">
                <div class="usr-metric-item">
                    <span>{{ $text('Visible', 'المعروض') }}</span>
                    <strong>{{ $visibleCount }}</strong>
                </div>

                <div class="usr-metric-item">
                    <span>{{ $text('Total', 'الإجمالي') }}</span>
                    <strong>{{ $totalCount }}</strong>
                </div>

                <div class="usr-metric-item">
                    <span>{{ $text('Email verified', 'إيميل موثق') }}</span>
                    <strong>{{ $emailVerifiedCount }}</strong>
                </div>

                <div class="usr-metric-item">
                    <span>{{ $text('Phone verified', 'هاتف موثق') }}</span>
                    <strong>{{ $phoneVerifiedCount }}</strong>
                </div>
            </div>
        </section>

        <section class="usr-filter-card">
            <div class="usr-section-head">
                <div>
                    <h5>{{ $text('Search and sorting', 'البحث والترتيب') }}</h5>
                    <p>
                        {{ $text(
                            'Find users by name, username, email, or phone.',
                            'ابحث عن المستخدمين بالاسم أو اسم المستخدم أو البريد أو الهاتف.'
                        ) }}
                    </p>
                </div>

                @if (request('search') || request('sort_by') || request('sort_dir'))
                    <a href="{{ $baseUsersUrl }}" class="btn btn-sm usr-clear-btn">
                        <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Reset', 'إعادة ضبط') }}
                    </a>
                @endif
            </div>

            <div class="usr-quick-filters">
                @foreach ($quickSorts as $sort)
                    @php
                        $sortLink = route('admin.users', array_merge(
                            request()->except(['page', 'sort_by', 'sort_dir']),
                            $sort['params']
                        ));
                    @endphp

                    <a href="{{ $sortLink }}" class="usr-quick-chip {{ $sort['active'] ? 'active' : '' }}">
                        {{ $sort['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.users') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-7">
                        <label class="usr-label" for="usersSearch">
                            {{ $text('Search users', 'بحث المستخدمين') }}
                        </label>

                        <div class="usr-input-icon">
                            <i class="fas fa-search"></i>

                            <input
                                id="usersSearch"
                                type="text"
                                name="search"
                                class="form-control usr-control"
                                placeholder="{{ $text('Search by name, username, email, or phone...', 'ابحث بالاسم أو اليوزر أو الإيميل أو الهاتف...') }}"
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                    <div class="col-12 col-lg-5">
                        <div class="usr-filter-actions">
                            <button type="submit" class="btn btn-primary usr-submit-btn">
                                <i class="fas fa-filter {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Apply', 'تطبيق') }}
                            </button>

                            <a href="{{ route('admin.users') }}" class="btn usr-reset-btn">
                                <i class="fas fa-undo {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Reset', 'إعادة ضبط') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="usr-table-card">
            <div class="usr-section-head usr-table-head">
                <div>
                    <h5>{{ __('admin-dashboard.all_users') }}</h5>
                    <p>
                        {{ $text(
                            'Showing key user information only to keep the table clean.',
                            'يتم عرض أهم بيانات المستخدم فقط للحفاظ على شكل الجدول واضح.'
                        ) }}
                    </p>
                </div>

                <span class="usr-page-count">
                    <i class="fas fa-users {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $visibleCount }} / {{ $totalCount }}
                </span>
            </div>

            <div class="card-body p-0">
                @if($users->count() > 0)
                    <div class="table-responsive usr-table-wrap">
                        <table class="table align-middle mb-0 usr-table">
                            <thead>
                                <tr>
                                    <th class="usr-col-user">
                                        <a class="usr-sort-link" href="{{ $sortUrl('username') }}">
                                            <span>{{ __('admin-dashboard.user_name') }}</span>
                                            <i class="fas {{ $sortIcon('username') }}"></i>
                                        </a>
                                    </th>

                                    <th class="usr-col-contact">
                                        {{ $text('Contact', 'التواصل') }}
                                    </th>

                                    <th class="usr-col-orders">
                                        {{ $text('Activity', 'النشاط') }}
                                    </th>

                                    <th class="usr-col-date">
                                        <a class="usr-sort-link" href="{{ $sortUrl('created_at') }}">
                                            <span>{{ __('admin-dashboard.join_date') }}</span>
                                            <i class="fas {{ $sortIcon('created_at') }}"></i>
                                        </a>
                                    </th>

                                    <th class="usr-col-actions text-end">
                                        {{ __('admin-dashboard.actions') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($users as $user)
                                    @php
                                        $displayName = $user->name ?: ($user->username ?: $text('User', 'مستخدم'));
                                        $initial = mb_substr($displayName, 0, 1);
                                        $walletBalance = optional($user->wallet)->balance ?? 0;
                                        $walletCurrency = optional($user->wallet)->currency ?? '';
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="usr-user-cell">
                                                @if($user->avatar)
                                                    <img
                                                        src="{{ asset($user->avatar) }}"
                                                        alt="{{ $displayName }}"
                                                        class="usr-avatar-img"
                                                    >
                                                @else
                                                    <span class="usr-avatar">
                                                        {{ $initial }}
                                                    </span>
                                                @endif

                                                <div class="usr-user-info">
                                                    <div class="usr-user-name">
                                                        {{ $displayName }}
                                                    </div>

                                                    <div class="usr-user-meta">
                                                        <span class="usr-user-number">
                                                            {{ $user->user_number ?: '#' . $user->id }}
                                                        </span>

                                                        @if($user->username)
                                                            <span class="usr-dot">•</span>
                                                            <span>
                                                                <i class="fas fa-at"></i>
                                                                {{ $user->username }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="usr-contact-stack">
                                                <div class="usr-contact-line">
                                                    <i class="far fa-envelope"></i>

                                                    @if($user->email)
                                                        <a href="mailto:{{ $user->email }}">
                                                            {{ $user->email }}
                                                        </a>
                                                    @else
                                                        <span>{{ __('admin-dashboard.not_provided') }}</span>
                                                    @endif

                                                    @if($user->email_verified_at)
                                                        <span class="usr-mini-badge success">
                                                            {{ __('admin-dashboard.verified') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="usr-contact-line">
                                                    <i class="fas fa-phone"></i>

                                                    @if($user->phone)
                                                        <a href="tel:{{ $user->phone }}">
                                                            {{ $user->phone }}
                                                        </a>

                                                        <span class="usr-mini-badge {{ $user->phone_verified_at ? 'success' : 'danger' }}">
                                                            {{ __('admin-dashboard.' . ($user->phone_verified_at ? 'verified' : 'not_verified')) }}
                                                        </span>
                                                    @else
                                                        <span>{{ __('admin-dashboard.not_provided') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="usr-activity-pills">
                                                <span class="usr-count-pill shipment">
                                                    <i class="fas fa-shipping-fast"></i>
                                                    {{ $user->orders_count }}
                                                </span>

                                                <span class="usr-count-pill ecommerce">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    {{ $user->ecommerce_orders_count }}
                                                </span>

                                                <span class="usr-count-pill wallet">
                                                    <i class="fas fa-wallet"></i>
                                                    {{ $walletBalance }} {{ $walletCurrency }}
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="usr-date">
                                                @include('admin.partials.date', ['date' => $user->created_at])
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <div class="usr-actions-group">
                                                <a
                                                    href="{{ route('admin.users.show', $user->id) }}"
                                                    class="btn btn-sm usr-view-btn"
                                                    title="{{ __('admin-dashboard.view_details') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                    <span>{{ $text('View', 'عرض') }}</span>
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm usr-wallet-btn js-wallet-btn"
                                                    title="{{ __('admin-dashboard.wallet_adjust') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}"
                                                    data-balance="{{ $walletBalance }}"
                                                    data-currency="{{ $walletCurrency }}"
                                                >
                                                    <i class="fas fa-wallet"></i>
                                                    <span>{{ $text('Wallet', 'المحفظة') }}</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="usr-pagination">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="usr-empty">
                        <div class="usr-empty-icon">
                            <i class="fas fa-users"></i>
                        </div>

                        <h5>{{ __('admin-dashboard.no_users_found') }}</h5>

                        <p>
                            {{ __(request('search') ? 'admin-dashboard.no_users_search' : 'admin-dashboard.no_users_yet') }}
                        </p>

                        @if(request('search'))
                            <a href="{{ route('admin.users') }}" class="btn usr-view-btn">
                                <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Clear search', 'مسح البحث') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div class="modal fade" id="userActionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content usr-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="userActionModalLabel">
                        {{ __('admin-dashboard.confirm_action') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="userActionModalBody">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn usr-reset-btn" data-bs-dismiss="modal">
                        {{ __('admin-dashboard.cancel') }}
                    </button>

                    <form id="userActionForm" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn" id="userActionButton">
                            {{ __('admin-dashboard.confirm') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="walletBalanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content usr-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="walletBalanceModalLabel">
                            {{ __('admin-dashboard.wallet_adjust') }}
                        </h5>
                        <p class="usr-modal-subtitle mb-0">
                            {{ $text('Update the selected user wallet balance safely.', 'عدّل رصيد محفظة المستخدم المحدد بأمان.') }}
                        </p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="walletBalanceForm" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="modal-body">
                        <div class="usr-wallet-current mb-3">
                            <span>{{ __('admin-dashboard.wallet_current_balance') }}</span>

                            <div class="input-group">
                                <input type="text" class="form-control usr-control" id="walletCurrentBalance" readonly>
                                <span class="input-group-text" id="walletCurrency">-</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="usr-label">
                                    {{ __('admin-dashboard.wallet_operation') }}
                                </label>

                                <select name="operation" class="form-select usr-control" required>
                                    <option value="add">{{ __('admin-dashboard.wallet_add') }}</option>
                                    <option value="subtract">{{ __('admin-dashboard.wallet_subtract') }}</option>
                                </select>
                            </div>

                            <div class="col-md-7">
                                <label class="usr-label">
                                    {{ __('admin-dashboard.wallet_amount') }}
                                </label>

                                <input
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    class="form-control usr-control"
                                    required
                                >
                            </div>
                        </div>

                        <div class="usr-helper-note mt-3">
                            <i class="fas fa-info-circle {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                            {{ __('admin-dashboard.wallet_note') }}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn usr-reset-btn" data-bs-dismiss="modal">
                            {{ __('admin-dashboard.cancel') }}
                        </button>

                        <button type="submit" class="btn btn-success usr-wallet-submit">
                            <i class="fas fa-check {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                            {{ __('admin-dashboard.wallet_update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmUserBlock(userId) {
            const modal = document.getElementById('userActionModal');
            const modalTitle = document.getElementById('userActionModalLabel');
            const modalBody = document.getElementById('userActionModalBody');
            const actionForm = document.getElementById('userActionForm');
            const actionButton = document.getElementById('userActionButton');

            modalTitle.textContent = '{{ __("admin-dashboard.block_user_title") }}';
            modalBody.textContent = '{{ __("admin-dashboard.block_user_message") }}';
            actionForm.action = `/admin/users/${userId}/block`;
            actionButton.className = 'btn btn-danger';
            actionButton.textContent = '{{ __("admin-dashboard.block_user") }}';

            new bootstrap.Modal(modal).show();
        }

        function confirmUserUnblock(userId) {
            const modal = document.getElementById('userActionModal');
            const modalTitle = document.getElementById('userActionModalLabel');
            const modalBody = document.getElementById('userActionModalBody');
            const actionForm = document.getElementById('userActionForm');
            const actionButton = document.getElementById('userActionButton');

            modalTitle.textContent = '{{ __("admin-dashboard.unblock_user_title") }}';
            modalBody.textContent = '{{ __("admin-dashboard.unblock_user_message") }}';
            actionForm.action = `/admin/users/${userId}/unblock`;
            actionButton.className = 'btn btn-success';
            actionButton.textContent = '{{ __("admin-dashboard.unblock_user") }}';

            new bootstrap.Modal(modal).show();
        }
    </script>

    <style>
        .usr-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .usr-hero-card,
        .usr-filter-card,
        .usr-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
        }

        .usr-hero-card,
        .usr-filter-card {
            padding: 1.25rem;
        }

        .usr-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .usr-hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .usr-chip {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .28rem .7rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 800;
        }

        .usr-hero-text h4 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .usr-hero-text p {
            margin: .45rem 0 0;
            max-width: 820px;
            color: #64748b;
            font-size: .95rem;
            line-height: 1.8;
        }

        .usr-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1.15rem;
        }

        .usr-metric-item {
            min-height: 74px;
            padding: .85rem 1rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .usr-metric-item span {
            color: #64748b;
            font-size: .78rem;
            font-weight: 800;
            margin-bottom: .35rem;
        }

        .usr-metric-item strong {
            color: #111827;
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1;
        }

        .usr-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .usr-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 900;
        }

        .usr-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .88rem;
            line-height: 1.6;
        }

        .usr-clear-btn,
        .usr-reset-btn {
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-weight: 800;
        }

        .usr-clear-btn:hover,
        .usr-reset-btn:hover {
            background: #f8fafc;
            color: #111827;
        }

        .usr-quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .usr-quick-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: .35rem .8rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .82rem;
            font-weight: 800;
            text-decoration: none;
        }

        .usr-quick-chip:hover,
        .usr-quick-chip.active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .usr-label {
            display: block;
            margin-bottom: .4rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
        }

        .usr-control {
            min-height: 44px;
            border-radius: 13px;
            border-color: #dbe3ea;
            color: #111827;
            font-size: .9rem;
            box-shadow: none;
        }

        .usr-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .usr-input-icon {
            position: relative;
        }

        .usr-input-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        [dir="ltr"] .usr-input-icon i {
            left: .85rem;
        }

        [dir="rtl"] .usr-input-icon i {
            right: .85rem;
        }

        [dir="ltr"] .usr-input-icon .usr-control {
            padding-left: 2.5rem;
        }

        [dir="rtl"] .usr-input-icon .usr-control {
            padding-right: 2.5rem;
        }

        .usr-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .usr-submit-btn {
            min-height: 44px;
            border-radius: 13px;
            font-weight: 800;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .12);
        }

        .usr-table-card {
            overflow: hidden;
        }

        .usr-table-head {
            padding: 1.25rem 1.25rem 0;
        }

        .usr-page-count {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .42rem .8rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .usr-table-wrap {
            border-top: 1px solid #e5e7eb;
        }

        .usr-table {
            min-width: 920px;
            table-layout: fixed;
        }

        .usr-table thead th {
            padding: .85rem 1rem;
            background: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .usr-table tbody td {
            padding: 1rem;
            border-color: #eef2f7;
            vertical-align: middle;
        }

        .usr-table tbody tr:hover {
            background: #fbfdff;
        }

        .usr-col-user {
            width: 30%;
        }

        .usr-col-contact {
            width: 28%;
        }

        .usr-col-orders {
            width: 20%;
        }

        .usr-col-date {
            width: 12%;
        }

        .usr-col-actions {
            width: 10%;
        }

        .usr-sort-link {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }

        .usr-sort-link:hover {
            color: #1d4ed8;
        }

        .usr-user-cell {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .usr-avatar,
        .usr-avatar-img {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            flex-shrink: 0;
        }

        .usr-avatar {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            text-transform: uppercase;
        }

        .usr-avatar-img {
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .usr-user-info {
            min-width: 0;
        }

        .usr-user-name {
            color: #111827;
            font-size: .95rem;
            font-weight: 900;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .usr-user-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .25rem;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.5;
        }

        .usr-user-number {
            color: #1d4ed8;
            font-weight: 900;
        }

        .usr-dot {
            color: #cbd5e1;
            padding-inline: .15rem;
        }

        .usr-contact-stack {
            display: grid;
            gap: .45rem;
        }

        .usr-contact-line {
            display: flex;
            align-items: center;
            gap: .45rem;
            color: #64748b;
            font-size: .82rem;
            min-width: 0;
        }

        .usr-contact-line i {
            color: #94a3b8;
            width: 16px;
            flex-shrink: 0;
        }

        .usr-contact-line a {
            color: #334155;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .usr-contact-line a:hover {
            color: #1d4ed8;
        }

        .usr-mini-badge {
            display: inline-flex;
            align-items: center;
            padding: .18rem .5rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .usr-mini-badge.success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .usr-mini-badge.danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .usr-activity-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .usr-count-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .usr-count-pill.shipment {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .usr-count-pill.ecommerce {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .usr-count-pill.wallet {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .usr-date {
            color: #475569;
            font-size: .82rem;
            font-weight: 700;
        }

        .usr-actions-group {
            display: flex;
            justify-content: flex-end;
            gap: .45rem;
            flex-wrap: wrap;
        }

        .usr-view-btn,
        .usr-wallet-btn {
            border-radius: 999px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            min-height: 34px;
            padding-inline: .8rem;
        }

        .usr-view-btn {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .usr-view-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .usr-wallet-btn {
            border: 1px solid #a7f3d0;
            background: #ecfdf5;
            color: #047857;
        }

        .usr-wallet-btn:hover {
            background: #047857;
            border-color: #047857;
            color: #fff;
        }

        .usr-pagination {
            padding: 1rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            justify-content: center;
        }

        .usr-empty {
            padding: 4rem 1.5rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .usr-empty-icon {
            width: 78px;
            height: 78px;
            margin: 0 auto 1rem;
            border-radius: 26px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
        }

        .usr-empty h5 {
            color: #111827;
            font-weight: 900;
            margin-bottom: .45rem;
        }

        .usr-empty p {
            max-width: 520px;
            margin: 0 auto 1rem;
            color: #64748b;
            line-height: 1.8;
        }

        .usr-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .usr-modal .modal-header,
        .usr-modal .modal-footer {
            border-color: #e5e7eb;
        }

        .usr-modal .modal-title {
            color: #111827;
            font-weight: 900;
        }

        .usr-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .usr-wallet-current span {
            display: block;
            margin-bottom: .4rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 900;
        }

        .usr-helper-note {
            border-radius: 14px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #64748b;
            padding: .75rem .9rem;
            font-size: .82rem;
            line-height: 1.7;
        }

        .usr-wallet-submit {
            border-radius: 999px;
            font-weight: 900;
        }

        @media (max-width: 1199.98px) {
            .usr-table {
                min-width: 900px;
            }
        }

        @media (max-width: 767.98px) {
            .usr-hero-card,
            .usr-filter-card {
                padding: 1rem;
                border-radius: 18px;
            }

            .usr-hero-main {
                flex-direction: column;
            }

            .usr-hero-text h4 {
                font-size: 1.2rem;
            }

            .usr-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .usr-section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .usr-table-head {
                padding: 1rem 1rem 0;
            }

            .usr-filter-actions {
                justify-content: flex-start;
            }

            .usr-table {
                min-width: 860px;
            }

            .usr-actions-group {
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
        (function () {
            const modalEl = document.getElementById('walletBalanceModal');

            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const form = document.getElementById('walletBalanceForm');
            const currentBalanceEl = document.getElementById('walletCurrentBalance');
            const currencyEl = document.getElementById('walletCurrency');
            const walletModal = new bootstrap.Modal(modalEl);

            document.addEventListener('click', function (event) {
                const btn = event.target.closest('.js-wallet-btn');

                if (!btn) {
                    return;
                }

                event.preventDefault();

                const userId = btn.getAttribute('data-user-id');
                const balance = btn.getAttribute('data-balance') || '0';
                const currency = btn.getAttribute('data-currency') || '';

                currentBalanceEl.value = balance;
                currencyEl.textContent = currency || '-';

                form.action = `{{ url('/admin/users') }}/${userId}/wallet/balance`;

                walletModal.show();
            });
        })();
    </script>
@endsection