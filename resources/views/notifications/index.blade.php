// resources/views/notifications/index.blade.php
@extends('layouts.service')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Your Notifications</h2>
            <button onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800">
                Mark all as read
            </button>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <a href="{{ $notification->notification_type === 'order_update' ? route('orders.show', $notification->id) : '#' }}"
                   class="block px-6 py-4 hover:bg-gray-50 transition {{ !$notification->is_read ? 'bg-blue-50' : '' }}"
                   onclick="markAsRead('{{ $notification->id }}')">
                    <div class="flex justify-between">
                        <h3 class="font-medium">{{ $notification->title }}</h3>
                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-600 mt-1">{{ $notification->content }}</p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full 
                              {{ $notification->notification_type === 'order_update' ? 'bg-blue-100 text-blue-800' : '' }}
                              {{ $notification->notification_type === 'payment' ? 'bg-green-100 text-green-800' : '' }}
                              {{ $notification->notification_type === 'review' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ str_replace('_', ' ', $notification->notification_type) }}
                    </span>
                </a>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    You don't have any notifications yet.
                </div>
            @endforelse
        </div>
        
        {{ $notifications->links() }}
    </div>
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        document.querySelector(`a[onclick="markAsRead('${notificationId}')"]`)
            ?.classList.remove('bg-blue-50');
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => window.location.reload());
}
</script>
@endpush
@endsection