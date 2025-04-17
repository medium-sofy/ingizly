@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100">
    <div class="mb-6">
        <a href="{{ route('buyer.orders.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        {{-- Order Header --}}
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h2>
                        <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 
                               ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 
                               'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Service Details --}}
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Service Details</h3>
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/4 mb-4 md:mb-0">
                        @if($order->service->images->isNotEmpty())
                            <img src="{{ Storage::url($order->service->images->first()->image_url) }}" alt="{{ $order->service->title }}" class="w-full h-32 object-cover rounded">
                        @else
                            <div class="w-full h-32 bg-gray-200 flex items-center justify-center rounded">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="md:w-3/4 md:pl-6">
                        <h4 class="text-xl font-medium text-gray-800">{{ $order->service->title }}</h4>
                        <p class="text-gray-600 mt-2">{{ Str::limit($order->service->description, 150) }}</p>
                        <div class="mt-4 flex flex-wrap gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">Category:</span>
                                <span class="text-gray-800">{{ $order->service->category->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Provider:</span>
                                <span class="text-gray-800">{{ $order->service->provider->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Service Type:</span>
                                <span class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $order->service->service_type)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Order Details --}}
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Order Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm block">Scheduled Date:</span>
                            <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->scheduled_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm block">Scheduled Time:</span>
                            <span class="text-gray-800">{{ \Carbon\Carbon::parse($order->scheduled_time)->format('h:i A') }}</span>
                        </div>
                        @if($order->location)
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm block">Location:</span>
                            <span class="text-gray-800">{{ $order->location }}</span>
                        </div>
                        @endif
                    </div>
                    <div>
                        @if($order->special_instructions)
                        <div class="mb-4">
                            <span class="text-gray-500 text-sm block">Special Instructions:</span>
                            <p class="text-gray-800">{{ $order->special_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- Payment Details --}}
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Details</h3>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Service Price</span>
                    <span class="font-medium">{{ $order->total_amount }} EGP</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-200 mt-2">
                    <span class="text-gray-800 font-semibold">Total</span>
                    <span class="text-green-600 font-bold">{{ $order->total_amount }} EGP</span>
                </div>
            </div>
        </div>
        
        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-between">
            @if($order->status == 'pending')
            <form action="{{ route('buyer.orders.destroy', $order->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out" onclick="return confirm('Are you sure you want to cancel this order?')">
                    <i class="fas fa-times-circle mr-2"></i> Cancel Order
                </button>
            </form>
            @endif
            
            @if($order->status == 'completed')
            <a href="#" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out text-center">
                <i class="fas fa-star mr-2"></i> Leave a Review
            </a>
            @endif
            
            <a href="{{ route('buyer.orders.index') }}" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out text-center">
                <i class="fas fa-list mr-2"></i> Back to Orders
            </a>
        </div>
    </div>
</div>
@endsection