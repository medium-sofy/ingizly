@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Violations Reports</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.index') }}" method="GET">
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" name="search" placeholder="Search violations..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <!-- Status Filter -->
                    <select name="status" class="border border-gray-300 rounded p-2">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>

                    <!-- Date Range -->
                    <div class="flex items-center">
                        <input type="date" name="start_date"
                               value="{{ request('start_date') }}"
                               class="border border-gray-300 rounded p-2">
                        <span class="px-2">to</span>
                        <input type="date" name="end_date"
                               value="{{ request('end_date') }}"
                               class="border border-gray-300 rounded p-2">
                    </div>

                    <!-- Apply Filters Button -->
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Violations Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-left font-medium text-gray-700">Service</th>
                        <th class="py-3 text-left font-medium text-gray-700">Reported By</th>
                        <th class="py-3 text-left font-medium text-gray-700">Reason</th>
                        <th class="py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="py-3 text-left font-medium text-gray-700">Reported At</th>
                        <th class="py-3 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($violations as $violation)
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <div>
                                    <p class="font-medium">{{ $violation->service->title }}</p>
                                    <p class="text-sm text-gray-500">Provider: {{ $violation->service->provider->user->name }}</p>
                                </div>
                            </td>
                            <td class="py-4">
                                <div>
                                    <p class="font-medium">{{ $violation->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $violation->user->email }}</p>
                                </div>
                            </td>
                            <td class="py-4">
                                <p class="text-sm">{{ Str::limit($violation->reason, 100) }}</p>
                            </td>
                            <td class="py-4">
                                <span class="inline-block px-2 py-1 text-xs {{
                                    $violation->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($violation->status == 'investigating' ? 'bg-blue-100 text-blue-800' :
                                    ($violation->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
                                }} rounded-full">
                                    {{ ucfirst($violation->status) }}
                                </span>
                            </td>
                            <td class="py-4">
                                {{ $violation->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.reports.show', $violation->id) }}"
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                        View
                                    </a>
                                    <form action="{{ route('admin.reports.update', $violation->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                                class="border border-gray-300 rounded p-1 text-sm">
                                            <option value="pending" {{ $violation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="investigating" {{ $violation->status == 'investigating' ? 'selected' : '' }}>Investigating</option>
                                            <option value="resolved" {{ $violation->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="dismissed" {{ $violation->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">No violations found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing {{ $violations->firstItem() ?? 0 }} to {{ $violations->lastItem() ?? 0 }} of {{ $violations->total() ?? 0 }} results
                </div>
                <div class="flex">
                    {{ $violations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
