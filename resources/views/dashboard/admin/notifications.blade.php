@extends('layouts.admin')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;
@endphp

@section('title', $text('Notifications', 'الإشعارات'))
@section('page-title', $text('Notifications', 'الإشعارات'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
        {{ $text('Back to Dashboard', 'العودة إلى لوحة التحكم') }}
    </a>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $text('All Notifications', 'جميع الإشعارات') }}</h5>

        @if(auth('admin')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.readAll') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success">
                    {{ $text('Mark All as Read', 'تعيين الكل كمقروء') }}
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
                            <strong class="d-block text-primary mb-1">
                                {{ $text('Notification', 'الإشعار') }} #{{ $notification->notification_number ?? 'NOT-00000000' }}
                            </strong>
                            <strong>{{ $notification->data['title'] ?? $text('Notification', 'الإشعار') }}</strong><br>
                            <small>{{ $notification->data['body'] ?? '' }}</small><br>
                            <small class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>

                        @if(!$notification->read_at)
                            <span class="badge bg-primary">{{ $text('New', 'جديد') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">{{ $text('No notifications found.', 'لا توجد إشعارات.') }}</p>
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
