@extends('layouts.app')

@section('content')
    <div class="py-4">
        {{-- Header --}}
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <h1 class="text-3xl font-bold">Payments List</h1>
            {{-- Adding new payments manually is not typical, removed Add New button --}}
            {{-- <a href="{{ route('admin.payments.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded whitespace-nowrap">
                Add New Payment
            </a> --}}
            <div class="flex space-x-4">
                <a href="{{ route('admin.payments.export.pdf') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Export PDF
                </a>
                <a href="{{ route('admin.payments.export.csv') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Export CSV
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            {{-- Assuming a route named 'admin.payments.index' for filtering --}}
            <form action="{{ route('admin.payments') }}" method="GET">
                {{-- Search Bar --}}
                <div class="mb-4">
                    <label for="search" class="sr-only">Search Payments</label>
                    <input type="text" name="search" id="search" placeholder="Search by transaction ID, order ID, gateway..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Filter Controls --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    {{-- Date Range Filter (e.g., creation date) --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <label for="date_from" class="sr-only">Date From</label>
                        <input type="date" name="date_from" id="date_from" title="Payment date from"
                               value="{{ request('date_from') }}"
                               class="border border-gray-300 rounded p-2">
                        <span class="hidden sm:inline px-2">-</span>
                        <label for="date_to" class="sr-only">Date To</label>
                        <input type="date" name="date_to" id="date_to" title="Payment date to"
                               value="{{ request('date_to') }}"
                               class="border border-gray-300 rounded p-2">
                    </div>

                    {{-- Payment Status Filter --}}
                    <div>
                        <label for="payment_status" class="sr-only">Status</label>
                        <select name="payment_status" id="payment_status"
                                class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{-- Using empty value for 'all' status --}}
                            <option value="" {{ request('payment_status') == '' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="successful" {{ request('payment_status') == 'successful' ? 'selected' : '' }}>Successful</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            {{-- Add other statuses if needed --}}
                        </select>
                    </div>

                    {{-- Payment Gateway Filter (Optional, based on your needs) --}}
                    {{-- <div>
                        <label for="gateway" class="sr-only">Gateway</label>
                        <select name="gateway" id="gateway"
                                class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                             <option value="">All Gateways</option>
                             <option value="Paymob" {{ request('gateway') == 'Paymob' ? 'selected' : '' }}>Paymob</option>
                             {{-- Add other gateways here --}}
                    {{-- </select>
                </div> --}}


                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Apply Filters
                        </button>
                        {{-- Assuming a route named 'admin.payments.index' for resetting --}}
                        <a href="{{ route('admin.payments') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Payments Table --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="overflow-x-auto p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="p-3 text-left font-semibold text-gray-700">ID</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Order ID</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Gateway</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Amount</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Transaction ID</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="p-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- Loop through the payments collection --}}
                    @forelse($payments as $payment)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="p-3 text-sm text-gray-700">{{ $payment->id }}</td>
                            {{-- Link to the related order if applicable --}}
                            <td class="p-3 text-sm text-gray-700">
                                @if($payment->order_id)
                                    <a href="
{{--                                    {{ route('admin.orders.show', $payment->order_id) }}--}}
                                    " class="text-blue-600 hover:underline">
                                        {{ $payment->order_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="p-3 text-sm text-gray-700">{{ $payment->payment_gateway }}</td>
                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="p-3 text-sm text-gray-700">
                                {{-- Display status with colored badges --}}
                                <span class="inline-block px-2 py-0.5 text-xs font-medium {{
                                        $payment->payment_status == 'successful' ? 'bg-green-100 text-green-800' :
                                        ($payment->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        ($payment->payment_status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                    }} rounded-full">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                            </td>
                            <td class="p-3 text-sm text-gray-700 break-all">{{ $payment->transaction_id ?? 'N/A' }}</td> {{-- Use break-all for long IDs --}}
                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $payment->created_at->format('M d, Y H:i') }}</td> {{-- Display creation date/time --}}
                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    {{-- Add actions like viewing associated order or refund if applicable --}}
{{--                                    @if($payment->order_id)--}}
{{--                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded text-xs">--}}
{{--                                            View Order--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
                                    {{-- Add a button for refund/view payment details if needed --}}
                                    {{-- @if($payment->payment_status == 'successful')
                                        <button class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded text-xs">Refund</button>
                                    @endif --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Adjust colspan based on the number of columns --}}
                            <td colspan="8" class="py-6 text-center text-gray-500">No payments found matching your criteria.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($payments->hasPages())
                <div class="flex flex-wrap justify-between items-center p-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600 mb-2 md:mb-0">
                        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() ?? 0 }} results
                    </div>
                    <div>
                        {{-- Use withQueryString to preserve filters when paginating --}}
                        {{ $payments->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Removed user-specific delete script and forms --}}

@endsection

@push('scripts')
    {{-- Add any specific scripts for payment management if needed --}}
@endpush
