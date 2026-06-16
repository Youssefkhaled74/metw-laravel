@extends('layouts.vendor')

@section('title', __('vendor-dashboard.notifications'))
@section('page-title', __('vendor-dashboard.notifications'))

@section('page-actions')
    <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('vendor-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('vendor-dashboard.all_notifications') }}</h5>

        @if(auth('vendor')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('vendor.notifications.readAll') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">
                    {{ __('vendor-dashboard.mark_all_as_read') }}
                </button>
            </form>
        @endif
    </div>

    <div class="card-body">
        @if ($notifications->count() > 0)
            <ul class="list-group">
                @foreach ($notifications as $notification)
                    <li class="list-group-item d-flex justify-content-between align-items-center
                        {{ $notification->read_at ? 'bg-light' : '' }}"
                        style="cursor:pointer"
                        onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')">

                        <div>
                            <strong>{{ $notification->data['title'] ?? __('vendor-dashboard.notification') }}</strong><br>
                            <small>{{ $notification->data['body'] ?? '' }}</small><br>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>

                        @if(!$notification->read_at)
                            <span class="badge bg-primary">{{ __('vendor-dashboard.new') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">{{ __('vendor-dashboard.no_notifications_found') }}</p>
        @endif
    </div>
</div>

<script>
    function markAsReadAndRedirect(id, url) {
        fetch(`/vendor/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => {
            window.location.href = url;
        }).catch(() => {
            window.location.href = url;
        });
    }
</script>
@endsection
