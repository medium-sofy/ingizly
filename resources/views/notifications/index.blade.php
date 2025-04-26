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

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-white flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class='bx bx-bell mr-2 text-purple-600'></i> Your Notifications
            </h2>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Mark all as read
                </button>
            </form>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse ($notifications as $notification)
                @php
                    $markAsReadUrl = route('notifications.mark-read', $notification->id);
                    
                    // Create a link using NotificationController's logic
                    $user = auth()->user();
                    $violationId = null;
                    $reviewId = null;
                    $orderId = null;
                    $serviceId = null;
                    $source = null;
                    
                    // Try to decode JSON content for source information
                    try {
                        $decodedContent = json_decode($notification->content, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedContent)) {
                            $source = $decodedContent['source'] ?? null;
                            $displayContent = $decodedContent['message'] ?? $notification->content;
                            $contentForRegex = $displayContent;
                        } else {
                            $displayContent = $notification->content;
                            $contentForRegex = $notification->content;
                        }
                    } catch (\Exception $e) {
                        $displayContent = $notification->content;
                        $contentForRegex = $notification->content;
                    }
                    
                    // Extract violation ID from title or content
                    if (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $notification->title, $matches)) {
                        $violationId = $matches[1] ?? $matches[2] ?? null;
                    } elseif (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $contentForRegex, $matches)) {
                        $violationId = $matches[1] ?? $matches[2] ?? null;
                    }
                    
                    // Extract review ID
                    if (preg_match('/review.*?#(\d+)/i', $contentForRegex, $matches)) {
                        $reviewId = $matches[1] ?? null;
                    }
                    
                    // Extract order ID
                    if (preg_match('/order.*?#(\d+)|booking.*?#(\d+)/i', $contentForRegex, $matches)) {
                        $orderId = $matches[1] ?? $matches[2] ?? null;
                    } elseif (preg_match('/order.*?#(\d+)|booking.*?#(\d+)/i', $notification->title, $matches)) {
                        $orderId = $matches[1] ?? $matches[2] ?? null;
                    }
                    
                    // Extract service ID
                    if (preg_match('/service id: (\d+)/i', $contentForRegex, $matches)) {
                        $serviceId = $matches[1] ?? null;
                    } elseif (preg_match('/\(service id: (\d+)\)/i', $contentForRegex, $matches)) {
                        $serviceId = $matches[1] ?? null;
                    }
                    
                    // Admin - New Service Notification
                    if ($user->role === 'admin' && str_contains($notification->title, 'New Service Pending Approval')) {
                        $link = route('admin.dashboard');
                    }
                    // Provider - Approval/Rejection Notifications
                    elseif ($user->role === 'service_provider' && 
                          (str_contains($notification->title, 'Service Approved') || 
                           str_contains($notification->title, 'Service Rejected'))) {
                        $link = route('provider.services.index');
                    }
                    // For service buyer
                    elseif ($user->role === 'service_buyer') {
                        if ($source === 'dashboard') {
                            $link = route('buyer.orders.index');
                        } elseif ($source === 'landing' && $serviceId) {
                            $link = route('service.details', $serviceId);
                        } elseif ($orderId) {
                            $order = \App\Models\Order::find($orderId);
                            if ($order) {
                                $link = route('service.details', $order->service_id);
                            } else {
                                $link = route('buyer.orders.index');
                            }
                        } else {
                            $link = route('notifications.index');
                        }
                    }
                    // For admin reports
                    elseif ($user->role === 'admin' && $notification->notification_type === 'system' && $violationId) {
                        $link = route('admin.reports.show', $violationId);
                    }
                    // For admin reviews
                    elseif ($user->role === 'admin' && $notification->notification_type === 'review' && $reviewId) {
                        $link = route('admin.reviews.show', $reviewId);
                    }
                    elseif ($user->role === 'admin' && $notification->notification_type === 'review' && !$reviewId) {
                        $link = route('admin.reviews.index');
                    }
                    // Default
                    else {
                        $link = route('notifications.index');
                    }
                @endphp
                
                <a href="{{ $link }}" 
                   class="notification-link block px-6 py-4 hover:bg-gray-50 transition {{ $notification->is_read ? 'bg-white' : 'bg-purple-50' }}"
                   data-read-url="{{ $markAsReadUrl }}">
                   
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                @switch($notification->notification_type)
                                    @case('order_update')
                                        <i class='bx bx-cart text-xl'></i>
                                        @break
                                    @case('payment')
                                        <i class='bx bx-credit-card text-xl'></i>
                                        @break
                                    @case('message')
                                        <i class='bx bx-message-detail text-xl'></i>
                                        @break
                                    @case('system')
                                        @if(str_contains($notification->title, 'Service Approved'))
                                            <i class='bx bx-check-circle text-xl text-green-500'></i>
                                        @elseif(str_contains($notification->title, 'Service Rejected'))
                                            <i class='bx bx-x-circle text-xl text-red-500'></i>
                                        @elseif(str_contains($notification->title, 'New Service'))
                                            <i class='bx bx-plus-circle text-xl text-blue-500'></i>
                                        @else
                                            <i class='bx bx-flag text-xl'></i>
                                        @endif
                                        @break
                                    @case('review')
                                        <i class='bx bx-star text-xl'></i>
                                        @break
                                    @default
                                        <i class='bx bx-bell text-xl'></i>
                                @endswitch
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $notification->title }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        New
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 mt-1">{{ $displayContent }}</p>
                            <p class="text-sm text-gray-400 mt-2">
                                <i class='bx bx-time-five mr-1'></i> {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <i class='bx bx-bell-off text-4xl text-gray-400 mb-4'></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No notifications yet</h3>
                    <p class="text-gray-500">We'll notify you when something new arrives</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
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
                
                if (response.ok) {
                    // Redirect after successful mark-as-read
                    window.location.href = url;
                } else {
                    console.error('Failed to mark notification as read');
                    window.location.href = url; // Still redirect even if mark fails
                }
            } catch (error) {
                console.error('Error:', error);
                window.location.href = url; // Still redirect on error
            }
        });
    });
});
</script>
@endsection