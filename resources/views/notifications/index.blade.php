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
                    $link = $notification->notification_type === 'order_update' && $notification->order?->service_id 
                            ? route('service.details', $notification->order->service_id)
                            : '#';
                @endphp
                <a href="{{ $link }}" class="block px-6 py-4 hover:bg-gray-50 transition {{ $notification->is_read ? 'bg-white' : 'bg-purple-50' }}">
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
                            <p class="text-gray-600 mt-1">{{ $notification->content }}</p>
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
@endsection