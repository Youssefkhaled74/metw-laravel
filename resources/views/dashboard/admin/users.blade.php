@extends('layouts.admin')

@section('title', __('admin-dashboard.users_management'))
@section('page-title', __('admin-dashboard.users_management'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.users') }}</li>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_users') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-users me-2"></i>
                    {{ $users->count() }} / {{ $users->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            id="usersSearch"
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو اليوزر أو الإيميل أو الهاتف...' : 'Search by name, username, email, or phone...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $userNumberDir = request('sort_by') === 'user_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.users', array_merge(request()->except('page'), ['sort_by' => 'user_number', 'sort_dir' => $userNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.user_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'user_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'username' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.users', array_merge(request()->except('page'), ['sort_by' => 'username', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('admin-dashboard.user_name') }}</span>
                                        <i class="fas {{ request('sort_by') === 'username' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.user_email') }}</th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.user_phone') }}</th>
                                <th class="text-nowrap mobile-hide">{{ __('admin-dashboard.user_orders') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.users', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.join_date') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $user->user_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($user->avatar)
                                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle entity-logo" width="42" height="42">
                                            @else
                                                <div class="entity-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark entity-name">{{ $user->name }}</div>
                                                @if($user->username)
                                                    <small class="text-muted d-block entity-subname">
                                                        <i class="fas fa-at me-1"></i>
                                                        {{ $user->username }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="mobile-hide">
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                                        @if($user->email_verified_at)
                                            <br>
                                            <small class="badge bg-success px-2 py-1 mt-1">{{ __('admin-dashboard.verified') }}</small>
                                        @endif
                                    </td>
                                    <td class="mobile-hide">
                                        @if($user->phone)
                                            <a href="tel:{{ $user->phone }}" class="text-decoration-none">{{ $user->phone }}</a>
                                            <br>
                                            <small class="badge bg-{{ $user->phone_verified_at ? 'success' : 'danger' }} px-2 py-1 mt-1">
                                                {{ __('admin-dashboard.' . ($user->phone_verified_at ? 'verified' : 'not_verified')) }}
                                            </small>
                                        @else
                                            <span class="text-muted">{{ __('admin-dashboard.not_provided') }}</span>
                                        @endif
                                    </td>
                                    <td class="mobile-hide">
                                        <div class="d-flex flex-column gap-2">
                                            <span class="count-pill packages-pill">
                                                <i class="fas fa-shipping-fast me-1"></i>
                                                {{ $user->orders_count }}
                                            </span>
                                            <span class="count-pill orders-pill">
                                                <i class="fas fa-shopping-cart me-1"></i>
                                                {{ $user->ecommerce_orders_count }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $user->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view_details') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-success js-wallet-btn action-icon-btn"
                                                    title="{{ __('admin-dashboard.wallet_adjust') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}"
                                                    data-balance="{{ optional($user->wallet)->balance ?? 0 }}"
                                                    data-currency="{{ optional($user->wallet)->currency ?? '' }}">
                                                <i class="fas fa-wallet"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-users empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_users_found') }}</h5>
                        <p class="text-muted mb-0">{{ __(request('search') ? 'admin-dashboard.no_users_search' : 'admin-dashboard.no_users_yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Block/Unblock User Modal -->
    <div class="modal fade" id="userActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userActionModalLabel">{{ __('admin-dashboard.confirm_action') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userActionModalBody">
                    <!-- Content will be set via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                    <form id="userActionForm" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn" id="userActionButton">{{ __('admin-dashboard.confirm') }}</button>
                    </form>
                </div>
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

    <!-- Wallet Balance Modal -->
    <div class="modal fade" id="walletBalanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="walletBalanceModalLabel">{{ __('admin-dashboard.wallet_adjust') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="walletBalanceForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        {{-- <div class="mb-2">
                            <div class="small text-muted">{{ __('admin-dashboard.user_name') }}</div>
                            <div class="fw-semibold" id="walletUserName">-</div>
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label">{{ __('admin-dashboard.wallet_current_balance') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="walletCurrentBalance" readonly>
                                <span class="input-group-text" id="walletCurrency">-</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">{{ __('admin-dashboard.wallet_operation') }}</label>
                                <select name="operation" class="form-select" required>
                                    <option value="add">{{ __('admin-dashboard.wallet_add') }}</option>
                                    <option value="subtract">{{ __('admin-dashboard.wallet_subtract') }}</option>
                                </select>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label class="form-label">{{ __('admin-dashboard.wallet_amount') }}</label>
                                <input name="amount" type="number" step="0.01" min="0.01" class="form-control" required>
                            </div>
                        </div>

                        <small class="text-muted">{{ __('admin-dashboard.wallet_note') }}</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('admin-dashboard.wallet_update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
