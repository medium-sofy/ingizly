@extends('layouts.provider')

@section('content')
<div class="p-10 bg-gray-100 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold text-gray-800 dark:text-white">Bookings Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">Manage all your service bookings in one place</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-t-2xl">
                <h2 class="text-2xl font-semibold">Service Bookings</h2>
            </div>

            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full table-auto text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700 uppercase text-gray-600 dark:text-gray-300 text-xs">
                        <tr>
                            <th class="px-6 py-4 text-left">Order ID</th>
                            <th class="px-6 py-4 text-left">Service</th>
                            <th class="px-6 py-4 text-left">Client</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Date & Time</th>
                            <th class="px-6 py-4 text-left">Amount</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-300">
                                <td class="px-6 py-4">#{{ $order->id }}</td>
                                <td class="px-6 py-4">{{ $order->service->title }}</td>
                                <td class="px-6 py-4">{{ $order->buyer->user->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status == 'accepted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($order->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($order->status == 'in_progress') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                        @elseif($order->status == 'pending_approval') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                        @elseif($order->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($order->status == 'disapproved') bg-purple-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->status == 'accepted' ? 'Payment pending' : $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>{{ $order->scheduled_date ? date('M d, Y', strtotime($order->scheduled_date)) : 'Not scheduled' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->scheduled_time ? date('h:i A', strtotime($order->scheduled_time)) : '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold">{{ $order->total_amount }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        @if($order->status == 'pending')
                                            <form action="{{ route('provider.dashboard.accept', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Accept</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('provider.dashboard.reject', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Reject</span>
                                                </button>
                                            </form>
                                        @elseif($order->status == 'accepted' && $order->hasSuccessfulPayment())
                                            <form action=" {{route('provider.service.start',$order->id)}} " method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Start Service</span>
                                                </button>
                                            </form>
                                        @elseif($order->status == 'in_progress')
                                            <form action="{{ route('provider.service.complete', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Complete</span>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('provider.bookings.show', $order->id) }}" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>View Details</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-400 dark:text-gray-500">
                                    No bookings found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Mobile Cards -->
            <div class="space-y-6 p-4 md:hidden">
                @forelse($orders as $order)
                    <div class="bg-gray-50 dark:bg-gray-700 shadow rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-sm font-semibold text-gray-700 dark:text-white">Order #{{ $order->id }}</div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($order->status == 'accepted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($order->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($order->status == 'in_progress') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                @elseif($order->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($order->status == 'cancelled') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->status == 'accepted' ? 'Payment pending' : $order->status)) }}
                            </span>
                        </div>

                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-1"><strong>Service:</strong> {{ $order->service->title }}</div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-1"><strong>Client:</strong> {{ $order->buyer->user->name }}</div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-1">
                            <strong>Date & Time:</strong>
                            {{ $order->scheduled_date ? date('M d, Y', strtotime($order->scheduled_date)) : 'Not scheduled' }}
                            {{ $order->scheduled_time ? ' at ' . date('h:i A', strtotime($order->scheduled_time)) : '' }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-3"><strong>Amount:</strong> {{ $order->total_amount }}</div>

                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($order->status == 'pending')
                                <form action="{{ route('provider.dashboard.accept', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Accept</span>
                                    </button>
                                </form>
                                <form action="{{ route('provider.dashboard.reject', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Reject</span>
                                    </button>
                                </form>
                            @elseif($order->status == 'accepted' && $order->hasSuccessfulPayment())
                                <form action="{{ route('provider.dashboard.reject', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Start Service</span>
                                    </button>
                                </form>
                            @elseif($order->status == 'in_progress')
                                <form action="{{ route('provider.service.complete', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="submit" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Complete</span>
                                    </input>
                                </form>
                            @endif
                            <a href="{{ route('provider.bookings.show', $order->id) }}" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-full text-xs transition duration-300 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <span>View Details</span>
                            </a>
                        </div>

                        @if($order->special_instructions)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="text-sm text-gray-600 dark:text-gray-300"><strong>Special Instructions:</strong></div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->special_instructions }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-gray-400 dark:text-gray-500 py-10">
                        No bookings found.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                </div>
        </div>
    </div>
</div>
@endsection
