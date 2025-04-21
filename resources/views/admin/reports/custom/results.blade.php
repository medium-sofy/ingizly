    @extends('layouts.app')

    @section('content')
    <div class="container-fluid py-4"> {{-- Added py-4 for vertical padding --}}
        <div class="row"> {{-- Keep row if needed by your layout.app --}}
            <div class="col-12"> {{-- Keep col-12 if needed by your layout.app --}}
                <div class="bg-white rounded-lg shadow-sm"> {{-- Replaced card with bg-white, rounded-lg, shadow-sm --}}
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center"> {{-- Replaced card-header with p-6, border-b, flex justify-between --}}
                        <h3 class="text-xl font-semibold text-gray-800"> {{-- Replaced card-title with text-xl, font-semibold --}}
                            {{ ucwords(str_replace('_', ' ', $reportType)) }} Report
                        </h3>
                        <div class="flex space-x-2"> {{-- Replaced card-tools with flex space-x-2 --}}
                            <a href="{{ route('reports.custom.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded text-sm"> {{-- Replaced btn-secondary btn-sm --}}
                                <i class="fas fa-arrow-left mr-1"></i> Back {{-- Added mr-1 for spacing --}}
                            </a>
                            {{-- Ensure you have a library like SheetJS (XLSX) included globally or via a script tag --}}
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm" onclick="exportToExcel()"> {{-- Replaced btn-primary btn-sm --}}
                                <i class="fas fa-file-excel mr-1"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                    <div class="p-6"> {{-- Replaced card-body with p-6 --}}
                        @if($data->isEmpty())
                            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert"> {{-- Replaced alert alert-info --}}
                                <strong class="font-bold">Info!</strong> {{-- Added font-bold --}}
                                <span class="block sm:inline">No data found for this report.</span> {{-- Added block/inline for responsiveness --}}
                            </div>
                        @else
                            {{-- Added an ID for the export function --}}
                            <div class="overflow-x-auto"> {{-- Replaced table-responsive --}}
                                <table id="reportTable" class="min-w-full divide-y divide-gray-200"> {{-- Replaced table table-bordered table-striped --}}
                                    <thead>
                                    <tr class="border-b border-gray-200"> {{-- Added border-b --}}
                                        @switch($reportType)
                                            @case('user_transactions')
                                            @case('pending_transactions')
                                                <th class="p-3 text-left font-semibold text-gray-700">ID</th> {{-- Added table header styling --}}
                                                <th class="p-3 text-left font-semibold text-gray-700">Amount</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Currency</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Status</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Provider</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Service</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Order ID</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Date</th>
                                                @break

                                            @case('user_orders')
                                            @case('status_orders')
                                                <th class="p-3 text-left font-semibold text-gray-700">ID</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Status</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Amount</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Provider</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Service</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Scheduled Date</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Scheduled Time</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Created At</th>
                                                @break

                                            @case('service_performance')
                                                <th class="p-3 text-left font-semibold text-gray-700">ID</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Title</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Provider</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Category</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Price</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Orders</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Reviews</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Views</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Status</th>
                                                @break

                                            @case('provider_performance')
                                                <th class="p-3 text-left font-semibold text-gray-700">ID</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Name</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Email</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Services</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Total Orders</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Total Reviews</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Avg Rating</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Status</th>
                                                @break

                                            @case('category_performance')
                                                <th class="p-3 text-left font-semibold text-gray-700">Category</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Services</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Total Views</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Avg Price</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Total Orders</th>
                                                <th class="p-3 text-left font-semibold text-gray-700">Total Reviews</th>
                                                @break

                                            @case('revenue_analysis')
                                                {{-- Revenue analysis has a different structure, handled below --}}
                                                @break
                                        @endswitch
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @switch($reportType)
                                        @case('user_transactions')
                                        @case('pending_transactions')
                                            @foreach($data as $transaction)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50"> {{-- Added row styling --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ $transaction['id'] }}</td> {{-- Added cell styling --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ number_format($transaction['amount'], 2) }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $transaction['currency'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium {{
                                                                    $transaction['payment_status'] === 'successful' ? 'bg-green-100 text-green-800' :
                                                                    ($transaction['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                                    ($transaction['payment_status'] === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                                                }} rounded-full"> {{-- Used Tailwind badge styling --}}
                                                                {{ ucfirst($transaction['payment_status']) }}
                                                            </span>
                                                    </td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $transaction['provider'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ $transaction['service'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ $transaction['order_id'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $transaction['created_at']->format('Y-m-d H:i') }}</td> {{-- Added whitespace-nowrap --}}
                                                </tr>
                                            @endforeach
                                            @break

                                        @case('user_orders')
                                        @case('status_orders')
                                            @foreach($data as $order)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="p-3 text-sm text-gray-700">{{ $order['id'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium {{
                                                                    $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                                                    ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                                    ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                                                }} rounded-full"> {{-- Used Tailwind badge styling --}}
                                                                {{ ucfirst($order['status']) }}
                                                            </span>
                                                    </td>
                                                    <td class="p-3 text-sm text-gray-700">{{ number_format($order['total_amount'], 2) }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $order['provider'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ $order['service'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $order['scheduled_date'] ?? 'N/A' }}</td> {{-- Added whitespace-nowrap --}}
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $order['scheduled_time'] ?? 'N/A' }}</td> {{-- Added whitespace-nowrap --}}
                                                    <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $order['created_at']->format('Y-m-d H:i') }}</td> {{-- Added whitespace-nowrap --}}
                                                </tr>
                                            @endforeach
                                            @break

                                        @case('service_performance')
                                            @foreach($data as $service)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['id'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['title'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['provider'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['category'] ?? 'N/A' }}</td> {{-- Added N/A fallback --}}
                                                    <td class="p-3 text-sm text-gray-700">{{ number_format($service['price'], 2) }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['orders_count'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['reviews_count'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $service['view_count'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium {{ $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full"> {{-- Used Tailwind badge styling --}}
                                                                {{ ucfirst($service['status']) }}
                                                            </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @break

                                        @case('provider_performance')
                                            @foreach($data as $provider)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['id'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['name'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['email'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['services_count'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['total_orders'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $provider['total_reviews'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">
                                                        <div class="flex items-center"> {{-- Used flex items-center --}}
                                                            <span class="mr-2">{{ number_format($provider['avg_rating'], 1) }}</span> {{-- Added mr-2 --}}
                                                            <div class="rating text-sm"> {{-- Adjusted rating class, maybe text-sm --}}
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star{{ $i <= $provider['avg_rating'] ? ' text-yellow-500' : ' text-gray-300' }}"></i> {{-- Used Tailwind yellow-500 and gray-300 --}}
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="p-3 text-sm text-gray-700">
                                                            <span class="inline-block px-2 py-0.5 text-xs font-medium {{ $provider['is_verified'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full"> {{-- Used Tailwind badge styling --}}
                                                                {{ $provider['is_verified'] ? 'Verified' : 'Unverified' }}
                                                            </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @break

                                        @case('category_performance')
                                            @foreach($data as $category)
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    <td class="p-3 text-sm text-gray-700">{{ $category['category_name'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $category['services_count'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $category['total_views'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ number_format($category['avg_price'], 2) }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $category['total_orders'] }}</td>
                                                    <td class="p-3 text-sm text-gray-700">{{ $category['total_reviews'] }}</td>
                                                </tr>
                                            @endforeach
                                            @break

                                        @case('revenue_analysis')
                                            {{-- Revenue Analysis Display --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6"> {{-- Used grid layout --}}
                                                <div class="bg-blue-600 text-white rounded-lg p-4 shadow"> {{-- Card styling --}}
                                                    <h5 class="text-lg font-semibold mb-2">Total Revenue</h5> {{-- Title styling --}}
                                                    <p class="text-2xl font-bold">{{ number_format($data['total_revenue'], 2) }}</p> {{-- Value styling --}}
                                                </div>
                                                <div class="bg-green-600 text-white rounded-lg p-4 shadow"> {{-- Card styling --}}
                                                    <h5 class="text-lg font-semibold mb-2">Total Transactions</h5> {{-- Title styling --}}
                                                    <p class="text-2xl font-bold">{{ $data['total_transactions'] }}</p> {{-- Value styling --}}
                                                </div>
                                                {{-- Add other key metrics here if available in $data['revenue_analysis'] --}}
                                            </div>

                                            <h4 class="text-lg font-semibold mb-3 mt-6">Revenue by Service</h4> {{-- Added section title --}}
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead>
                                                    <tr class="border-b border-gray-200">
                                                        <th class="p-3 text-left font-semibold text-gray-700">Service</th>
                                                        <th class="p-3 text-left font-semibold text-gray-700">Total Revenue</th>
                                                        <th class="p-3 text-left font-semibold text-gray-700">Transaction Count</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($data['revenue_by_service'] as $service)
                                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                            <td class="p-3 text-sm text-gray-700">{{ $service['service_title'] }}</td>
                                                            <td class="p-3 text-sm text-gray-700">{{ number_format($service['total_amount'], 2) }}</td>
                                                            <td class="p-3 text-sm text-gray-700">{{ $service['transaction_count'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @break
                                    @endswitch
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    {{-- Include the SheetJS library for Excel export if you haven't already --}}
     <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> 
    <script>
        function exportToExcel() {
            const table = document.getElementById('reportTable');
            if (!table) {
                console.error("Table with ID 'reportTable' not found.");
                return;
            }
            const wb = XLSX.utils.table_to_book(table, {sheet: "Report"});
            XLSX.writeFile(wb, "{{ $reportType }}_report.xlsx");
        }
    </script>
    @endpush

    @push('styles')
    <style>
        /* Keep the rating star colors if they aren't fully handled by Tailwind classes */
        .rating .text-warning {
            color: #ffc107; /* Ensure this color is desired if not using Tailwind yellow */
        }
        /* Add any other specific styles here if needed */
    </style>
    @endpush