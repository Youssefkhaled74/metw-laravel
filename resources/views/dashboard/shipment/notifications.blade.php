@extends('layouts.shipment')

@section('title', __('shipment-dashboard.notifications'))
@section('page-title', __('shipment-dashboard.notifications'))

@section('page-actions')
    <a href="{{ route('shipment.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('shipment-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('shipment-dashboard.all_notifications') }}</h5>

        @if(auth('shipment')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('shipment.notifications.readAll') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">
                    {{ __('shipment-dashboard.mark_all_as_read') }}
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
                            <strong>{{ $notification->data['title'] ?? __('shipment-dashboard.notification') }}</strong><br>
                            <small>{{ $notification->data['body'] ?? '' }}</small><br>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>

                        @if(!$notification->read_at)
                            <span class="badge bg-primary">{{ __('shipment-dashboard.new') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">{{ __('shipment-dashboard.no_notifications_found') }}</p>
        @endif
    </div>
</div>

<script>
    function markAsReadAndRedirect(id, url) {
        fetch(`/shipment/notifications/${id}/read`, {
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
