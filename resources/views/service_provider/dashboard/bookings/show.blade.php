
@extends('layouts.provider')

@section('content')
    <div class="p-6 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen">
        <div class="mb-6">
            <a href="{{ route('provider.bookings.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-500 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Bookings
            </a>
        </div>

        <div class="max-w-4xl mx-auto">
            {{-- Order Header --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Order #{{ $booking->id }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Placed on {{ $booking->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                            {{ $booking->status == 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                               ($booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                               ($booking->status == 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300')) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                        </div>
                    </div>
                </div>

                {{-- Service Details --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Service Details</h3>
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/4 mb-4 md:mb-0">
                            @if($booking->service->images->isNotEmpty())
                                <img src="{{ Storage::url($booking->service->images->first()->image_url) }}" alt="{{ $booking->service->title }}" class="w-full h-32 object-cover rounded">
                            @else
                                <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                                    <i class="fas fa-image text-gray-400 dark:text-gray-500 text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="md:w-3/4 md:pl-6">
                            <h4 class="text-xl font-medium text-gray-800 dark:text-gray-100">{{ $booking->service->title }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ Str::limit($booking->service->description, 150) }}</p>
                            <div class="mt-4 flex flex-wrap gap-4">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Category:</span>
                                    <span class="text-gray-800 dark:text-gray-100">{{ $booking->service->category->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Provider:</span>
                                    <span class="text-gray-800 dark:text-gray-100">{{ $booking->service->provider->user->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">Service Type:</span>
                                    <span class="text-gray-800 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $booking->service->service_type)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Details --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Order Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <span class="text-gray-500 dark:text-gray-400 text-sm block">Scheduled Date:</span>
                                <span class="text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($booking->scheduled_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="text-gray-500 dark:text-gray-400 text-sm block">Scheduled Time:</span>
                                <span class="text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($booking->scheduled_time)->format('h:i A') }}</span>
                            </div>
                            @if($booking->location)
                                <div class="mb-4">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm block">Location:</span>
                                    <span class="text-gray-800 dark:text-gray-100">{{ $booking->location }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($booking->special_instructions)
                                <div class="mb-4">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm block">Special Instructions:</span>
                                    <p class="text-gray-800 dark:text-gray-100">{{ $booking->special_instructions }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Payment Details</h3>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 dark:text-gray-400">Service Price</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">{{ $booking->total_amount }} EGP</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                        <span class="text-gray-800 dark:text-gray-100 font-semibold">Total</span>
                        <span class="text-green-600 dark:text-green-400 font-bold">{{ $booking->total_amount }} EGP</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-between">
                @if($booking->status == 'pending')
                    <form action="{{ route('buyer.orders.destroy', $booking->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out" onclick="return confirm('Are you sure you want to cancel this order?')">
                            <i class="fas fa-times-circle mr-2"></i> Cancel Order
                        </button>
                    </form>
                @endif



                <a href="{{ route('buyer.orders.index') }}" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out text-center">
                    <i class="fas fa-list mr-2"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>
@endsection
