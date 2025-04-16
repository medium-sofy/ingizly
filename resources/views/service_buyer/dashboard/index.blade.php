@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100">
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 flex items-center">
        <i class="fas fa-tachometer-alt text-green-500 mr-3"></i> Dashboard
    </h2>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-shopping-cart text-green-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Total Orders</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $totalOrders }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-clock text-yellow-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Pending Orders</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $pendingOrders }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-check-circle text-blue-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Completed Orders</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $completedOrders }}</h3>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-6 shadow rounded flex items-center">
            <i class="fas fa-money-bill-wave text-green-500 text-2xl sm:text-3xl mr-3 sm:mr-4"></i>
            <div>
                <p class="text-gray-500 text-sm sm:text-base">Total Spent</p>
                <h3 class="text-xl sm:text-2xl font-semibold">{{ $totalSpent }} EGP</h3>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->service->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->service->provider->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->total_amount }} EGP
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No recent orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="{{ route('buyer.orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                View all orders <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection