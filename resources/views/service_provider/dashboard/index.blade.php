@extends('layouts.provider')

@section('content')
<div class="p-6 bg-gray-100">
    <h2 class="text-2xl font-bold mb-4">Service Provider Dashboard</h2>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 shadow rounded">
            <p class="text-gray-500">Total Services</p>
            <h3 class="text-xl font-semibold">{{ $totalServices }}</h3>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <p class="text-gray-500">Active Bookings</p>
            <h3 class="text-xl font-semibold">{{ $activeBookings }}</h3>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <p class="text-gray-500">Total Views</p>
            <h3 class="text-xl font-semibold">{{ $totalViews }}</h3>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <p class="text-gray-500">Average Rating</p>
            <h3 class="text-xl font-semibold">{{ number_format($averageRating, 1) ?? 'N/A' }}</h3>
        </div>
    </div>

    {{-- Services Table --}}
    <div class="bg-white p-4 shadow rounded mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-bold">Services</h3>
            <a  class="bg-blue-500 text-white px-4 py-2 rounded">Add New Service</a>
        </div>
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Bookings</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>{{ $service->title }}</td>
                    <td>
                        <span class="px-2 py-1 rounded text-white text-sm 
                            {{ $service->status == 'active' ? 'bg-green-500' : 'bg-yellow-500' }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </td>
                    <td>{{ $service->view_count }}</td>
                    <td>{{ $service->orders->count() }}</td>
                    <td>
                        <a href="{{ route('services.edit', $service->id) }}" class="text-blue-500">Edit</a> |
                        <form method="POST" action="{{ route('services.destroy', $service->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white p-4 shadow rounded mb-6">
        <h3 class="text-lg font-bold mb-3">Recent Bookings</h3>
        @foreach($recentOrders as $order)
        <div class="flex justify-between items-center mb-2 border-b pb-2">
            <div>
                <p class="font-semibold">{{ $order->buyer->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->service->title }} • {{ $order->scheduled_date }}</p>
            </div>
            <div>
                <button class="bg-green-500 text-white px-3 py-1 rounded">Accept</button>
                <button class="bg-red-500 text-white px-3 py-1 rounded">Reject</button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Recent Reviews --}}
    <div class="bg-white p-4 shadow rounded">
        <h3 class="text-lg font-bold mb-3">Recent Reviews</h3>
        @foreach($recentReviews as $review)
        <div class="border-b pb-2 mb-2">
            <p class="font-semibold">{{ $review->buyer->user->name }}</p>
            <p class="text-sm text-yellow-500">Rating: {{ $review->rating }} ★</p>
            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
