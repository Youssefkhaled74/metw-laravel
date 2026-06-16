@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Notifications</h5>

        @if(auth('admin')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.readAll') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">
                    Mark All as Read
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
                            <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong><br>
                            <small>{{ $notification->data['body'] ?? '' }}</small><br>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>

                        @if(!$notification->read_at)
                            <span class="badge bg-primary">New</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">No notifications found.</p>
        @endif
    </div>
</div>

<script>
    function markAsReadAndRedirect(id, url) {
        fetch(`/admin/notifications/${id}/read`, {
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
