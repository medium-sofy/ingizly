@php
    $layout = match(auth()->user()->role) {
        'service_buyer' => 'layouts.buyer',
        'service_provider' => 'layouts.provider',
        'admin' => 'layouts.sidbar',
        default => 'layouts.app'
    };
@endphp

@extends($layout)

@section('title', 'Notifications')

@push('styles')
<style>
    .notification-badge {
        transition: all 0.2s ease;
    }
    .notification-link {
        transition: background-color 0.2s ease;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                <i class='bx bx-bell mr-2 text-blue-600 dark:text-blue-400'></i> Your Notifications
            </h2>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition">
                    Mark all as read
                </button>
            </form>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($notifications as $notification)
                @php
                    $markAsReadUrl = route('notifications.mark-read', $notification->id);
                    
                    // Parse notification content
                    try {
                        $decodedContent = json_decode($notification->content, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedContent)) {
                            $displayContent = $decodedContent['message'] ?? $notification->content;
                            $notificationData = $decodedContent;
                        } else {
                            $displayContent = $notification->content;
                            $notificationData = [];
                        }
                    } catch (\Exception $e) {
                        $displayContent = $notification->content;
                        $notificationData = [];
                    }
                    
                    // Get link for this notification
                    $notificationController = new \App\Http\Controllers\NotificationController();
                    $link = $notificationController->getNotificationLink($notification);
                @endphp
                
                <a href="{{ $link }}" 
                   class="notification-link block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $notification->is_read ? 'bg-white dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20' }}"
                   data-read-url="{{ $markAsReadUrl }}">
                   
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                @switch($notification->notification_type)
                                    @case('order_update')
                                        <i class='bx bx-cart text-xl'></i>
                                        @break
                                    @case('payment')
                                        <i class='bx bx-credit-card text-xl'></i>
                                        @break
                                    @case('system')
                                        @if(isset($notificationData['status']))
                                            @switch($notificationData['status'])
                                                @case('resolved')
                                                    <i class='bx bx-check-circle text-xl text-green-500 dark:text-green-400'></i>
                                                    @break
                                                @case('dismissed')
                                                    <i class='bx bx-x-circle text-xl text-red-500 dark:text-red-400'></i>
                                                    @break
                                                @case('investigating')
                                                    <i class='bx bx-search-alt text-xl text-blue-500 dark:text-blue-400'></i>
                                                    @break
                                                @default
                                                    <i class='bx bx-flag text-xl'></i>
                                            @endswitch
                                        @else
                                            <i class='bx bx-flag text-xl'></i>
                                        @endif
                                        @break
                                    @default
                                        <i class='bx bx-bell text-xl'></i>
                                @endswitch
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $notification->title }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        New
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $displayContent }}</p>
                            @if(isset($notificationData['status']))
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                        @if($notificationData['status'] === 'resolved') 
                                            bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800
                                        @elseif($notificationData['status'] === 'dismissed') 
                                            bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800
                                        @elseif($notificationData['status'] === 'investigating') 
                                            bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800
                                        @else 
                                            bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-600 
                                        @endif">
                                        {{ ucfirst($notificationData['status']) }}
                                    </span>
                                </div>
                            @endif
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">
                                <i class='bx bx-time-five mr-1'></i> {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <i class='bx bx-bell-off text-4xl text-gray-400 dark:text-gray-500 mb-4'></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No notifications yet</h3>
                    <p class="text-gray-500 dark:text-gray-400">We'll notify you when something new arrives</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notification-link').forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const readUrl = this.getAttribute('data-read-url');

            try {
                // Mark as read via AJAX
                const response = await fetch(readUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                // Always redirect, even if marking as read fails
                window.location.href = url;
            } catch (error) {
                console.error('Error:', error);
                // Still redirect on error
                window.location.href = url;
            }
        });
    });
});
</script>
@endsection