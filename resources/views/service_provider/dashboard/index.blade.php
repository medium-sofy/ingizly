@extends('layouts.provider')

@section('content')


<style>
    [x-cloak] {
        display: none;
    }
</style>
<script src="//unpkg.com/alpinejs" defer></script>
<div class="p-4 sm:p-6 bg-gray-100">
    <!-- Welcome Message -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            Welcome, {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-600 text-sm sm:text-base">Here's an overview of your dashboard.</p>
    </div>

    <!-- Dashboard Header -->
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 flex items-center">
        <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i> Dashboard
    </h2>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-briefcase text-blue-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Total Services</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $totalServices }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-calendar-check text-green-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Pending Orders</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $pendingBookings }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-eye text-yellow-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Total Views</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $totalViews }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-star text-orange-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Average Rating</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ number_format($averageRating, 1) ?? 'N/A' }}</h3>
            </div>
        </div>
    </div>

    {{-- Services Table --}}
    <div class="bg-white p-4 sm:p-6 shadow rounded mb-8 overflow-x-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
            <h3 class="text-lg sm:text-xl font-bold flex items-center">
                <i class="fas fa-list text-blue-500 mr-2"></i> Services
            </h3>
            <a href="{{ route('provider.services.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm sm:text-base transition">
                <i class="fas fa-plus mr-2"></i> Add New Service
            </a>
        </div>
        <table class="min-w-full text-sm text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-xs sm:text-sm">
                    <th class="p-3 border-b">Service Name</th>
                    <th class="p-3 border-b">Status</th>
                    <th class="p-3 border-b">Views</th>
                    <th class="p-3 border-b">Bookings</th>
                    <th class="p-3 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border-b">{{ $service->title }}</td>
                    <td class="p-3 border-b">
                        @php
                            $statusStyles = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium 
                            {{ $statusStyles[$service->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </td>
                    <td class="p-3 border-b">{{ $service->view_count }}</td>
                    <td class="p-3 border-b">{{ $service->orders->count() }}</td>
                    <td class="p-3 border-b">
                        <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
                            <a href="{{ route('provider.services.edit', $service->id) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 text-xs sm:text-sm font-medium transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>

                            <!-- Delete Button with Modal -->
                            <div x-data="{ showConfirm{{ $service->id }}: false }" class="relative inline-block">
                                <button type="button"
                                        @click="showConfirm{{ $service->id }} = true"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 text-xs sm:text-sm font-medium transition">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>

                                <!-- Modal -->
                                <div x-show="showConfirm{{ $service->id }}" x-cloak
                                     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                    <div class="bg-white rounded shadow-lg w-full max-w-md mx-auto p-6">
                                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Delete Service</h2>
                                        <p class="text-gray-600 mb-6">Are you sure you want to delete this service? This action cannot be undone.</p>
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" @click="showConfirm{{ $service->id }} = false"
                                                    class="px-4 py-2 text-gray-600 hover:text-gray-800 bg-gray-100 rounded transition">
                                                Cancel
                                            </button>
                                            <form method="POST" action="{{ route('provider.services.destroy', $service->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition">
                                                    Yes, Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Delete Modal -->
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 p-3">No services found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white p-4 sm:p-6 shadow rounded mb-8">
        <h3 class="text-lg sm:text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-green-500 mr-2"></i> Recent Bookings
        </h3>
        @forelse($recentOrders as $order)
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b pb-4 gap-4">
            <div>
                <p class="font-semibold text-sm sm:text-base">{{ $order->buyer->user->name }}</p>
                <p class="text-xs sm:text-sm text-gray-500">{{ $order->service->title }} • {{ $order->scheduled_date }}</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs sm:text-sm">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs sm:text-sm">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-sm">No recent bookings found.</p>
        @endforelse
    </div>

    {{-- Recent Reviews --}}
    <div class="bg-white p-4 sm:p-6 shadow rounded">
        <h3 class="text-lg sm:text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-comments text-yellow-500 mr-2"></i> Recent Reviews
        </h3>
        @forelse($recentReviews as $review)
        <div class="border-b pb-4 mb-4">
            <p class="font-semibold text-sm sm:text-base">{{ $review->buyer->user->name }}</p>
            <p class="text-xs sm:text-sm text-yellow-500">Rating: {{ $review->rating }} ★</p>
            <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
        </div>
        @empty
        <p class="text-gray-500 text-sm">No recent reviews found.</p>
        @endforelse
    </div>
</div>

@endsection
