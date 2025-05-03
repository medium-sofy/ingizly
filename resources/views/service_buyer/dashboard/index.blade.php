
@extends('layouts.buyer')

@section('content')
<div class="p-6 sm:p-8 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen">
    <h2 class="text-3xl font-bold mb-8 flex items-center text-gray-800 dark:text-gray-100">
        <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i> Dashboard
    </h2>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-lg flex items-center">
            <i class="fas fa-shopping-cart text-blue-500 text-3xl mr-4"></i>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Total Orders</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalOrders }}</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-lg flex items-center">
            <i class="fas fa-clock text-yellow-500 text-3xl mr-4"></i>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Pending Orders</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingOrders }}</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-lg flex items-center">
            <i class="fas fa-check-circle text-green-500 text-3xl mr-4"></i>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Completed Orders</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $completedOrders }}</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-lg flex items-center">
            <i class="fas fa-money-bill-wave text-blue-500 text-3xl mr-4"></i>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Total Spent</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalSpent }} EGP</h3>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-blue-500 dark:bg-blue-600 text-white">
            <h3 class="text-lg font-bold">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $order->service->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-800 dark:text-gray-100">{{ $order->service->provider->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->total_amount }} EGP
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center flex justify-center text-sm font-medium">
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
                                <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-center text-blue-600 dark:text-blue-400 hover:underline mr-4">View</a>
                            @endif        
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No recent orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            <a href="{{ route('buyer.orders.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-500">
                View all orders <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection
