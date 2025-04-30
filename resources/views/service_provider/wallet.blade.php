@extends('layouts.provider')

@section('content')
<div class="p-10 bg-gray-100 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold text-gray-800 dark:text-white">Wallet Overview</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">Manage your payments and download transaction receipts easily</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-t-2xl">
                <h2 class="text-2xl font-semibold">Transaction History</h2>
            </div>

            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full table-auto text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700 uppercase text-gray-600 dark:text-gray-300 text-xs">
                        <tr>
                            <th class="px-6 py-4 text-left">Order ID</th>
                            <th class="px-6 py-4 text-left">Buyer Name</th>
                            <th class="px-6 py-4 text-left">Amount</th>
                            <th class="px-6 py-4 text-left">Transaction ID</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-300">
                                <td class="px-6 py-4">{{ $payment->order->id }}</td>
                                <td class="px-6 py-4">{{ $payment->order->user->name }}</td>
                                <td class="px-6 py-4 font-semibold">{{ $payment->amount }} {{ $payment->currency }}</td>
                                <td class="px-6 py-4">{{ $payment->transaction_id }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('provider.wallet.download', $payment->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full transition duration-300 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v2h-2V4H5v12h2v2H4a1 1 0 01-1-1V3zm9 6V5h-2v4H7l3 3 3-3h-2z" />
                                        </svg>
                                        <span>Download PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-400 dark:text-gray-500">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="space-y-6 mt-10 md:hidden">
                @forelse($payments as $payment)
                    <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm font-semibold text-gray-700 dark:text-white">Order #{{ $payment->order->id }}</div>
                            <a href="{{ route('provider.wallet.download', $payment->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-full transition duration-300 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v2h-2V4H5v12h2v2H4a1 1 0 01-1-1V3zm9 6V5h-2v4H7l3 3 3-3h-2z" />
                                </svg>
                                <span>PDF</span>
                            </a>
                        </div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-1"><strong>Buyer:</strong> {{ $payment->order->user->name }}</div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm mb-1"><strong>Amount:</strong> {{ $payment->amount }} {{ $payment->currency }}</div>
                        <div class="text-gray-600 dark:text-gray-300 text-sm"><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 dark:text-gray-500 py-10">
                        No transactions found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
