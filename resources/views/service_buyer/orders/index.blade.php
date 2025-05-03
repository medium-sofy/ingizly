@extends('layouts.buyer')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-white dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-10 flex items-center gap-4">
            <span class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-200 rounded-full">
                <i class="fas fa-shopping-cart text-xl"></i>
            </span>
            <span>My Orders</span>
        </h2>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 mb-10">
            <form action="{{ route('buyer.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-semibold shadow-md transition duration-300">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Orders List --}}
        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl overflow-hidden">
            @if($orders->isEmpty())
                <div class="p-10 text-center">
                    <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">You don't have any orders yet.</p>
                    <a href="{{ route('buyer.services.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full transition duration-300">
                        Browse Services
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Service</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Provider</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Date</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Amount</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->service->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $order->service->provider->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $order->created_at->format('M d, Y') }}<br>
                                        <span class="text-xs">{{ $order->scheduled_date }} {{ $order->scheduled_time }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @switch($order->status)
                                                @case('completed')
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                    @break
                                                @case('pending')
                                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                    @break
                                                @case('cancelled')
                                                @case('disapproved')
                                                    bg-purple-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                    @break
                                                @default
                                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @endswitch
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $order->status == 'accepted' ? 'Payment pending' : $order->status)) }}
                                       </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $order->total_amount }} EGP</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @if($order->status == 'pending')
                                            <form action="{{ route('buyer.orders.destroy', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this order?')" class="text-red-600 dark:text-red-400 hover:underline">Cancel</button>
                                            </form>
                                        @endif
                                        @if($order->status == 'pending_approval')
                                            <form action="{{ route('buyer.orders.approve', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"  class="text-green-800 bg-green-200 p-1 border rounded-xl mr-2 dark:text-green-800 hover:underline">Approve</button>
                                            </form>
                                            <form action="{{ route('buyer.orders.reject', $order->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Are you sure you want to Reject this order?')" class="text-red-800 bg-red-200 p-1 border rounded-xl mr-2 dark:text-red-800 hover:underline">Reject</button>
                                            </form>
                                        @endif
                                        @if($order->status == 'accepted')
                                            <div class="flex items-center space-x-2">
                                                <form action="{{ route('order.payment', $order->id) }}" method="GET" class="flex-shrink-0">
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-3 py-1.5 rounded-lg flex items-center justify-center shadow transition text-xs">
                                                        <i class="fas fa-credit-card mr-1"></i> Pay Now
                                                    </button>
                                                </form>
                                                <a href="{{ route('buyer.orders.show', $order->id) }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg flex items-center justify-center shadow transition text-xs">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                                <span class="text-xs bg-amber-300 px-3 py-1.5 rounded rounded-xl text-amber-800 dark:text-amber-400 flex items-center">
                                                    <i class="fas fa-lock-alt mr-1"></i> No cancellation
                                                </span>
                                            </div>
                                        @else
                                            <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-4">View</a>
                                        @endif                                    
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $orders->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
