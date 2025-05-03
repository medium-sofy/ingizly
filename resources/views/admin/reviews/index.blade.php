@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Reviews Management</h1>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reviews.index') }}" method="GET">
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" name="search" placeholder="Search reviews..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <!-- Rating Filter -->
                    <select name="rating" class="border border-gray-300 rounded p-2">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Stars
                            </option>
                        @endfor
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="border border-gray-300 rounded p-2">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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

        <!-- Reviews Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-left font-medium text-gray-700">Service</th>
                        <th class="py-3 text-left font-medium text-gray-700">Reviewer</th>
                        <th class="py-3 text-left font-medium text-gray-700">Rating</th>
                        <th class="py-3 text-left font-medium text-gray-700">Comment</th>
                        <th class="py-3 text-left font-medium text-gray-700">Status</th>
                        <th class="py-3 text-left font-medium text-gray-700">Date</th>
                        <th class="py-3 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reviews as $review)
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <div>
                                    <p class="font-medium">{{ $review->service->title }}</p>
                                    <p class="text-sm text-gray-500">Provider: {{ $review->service->provider->user->name }}</p>
                                </div>
                            </td>
                            <td class="py-4">
                                <div>
                                    <p class="font-medium">{{ $review->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->user->email }}</p>
                                </div>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="py-4">
                                <p class="text-sm">{{ Str::limit($review->comment, 100) }}</p>
                            </td>
                            <td class="py-4">
                                <span class="inline-block px-2 py-1 text-xs {{
                                    $review->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($review->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                                }} rounded-full">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td class="py-4">
                                {{ $review->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.reviews.show', $review->id) }}"
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                        View
                                    </a>
                                    <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                                class="border border-gray-300 rounded p-1 text-sm">
                                            <option value="pending" {{ $review->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $review->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $review->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded"
                                                onclick="return confirm('Are you sure you want to delete this review?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">No reviews found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing {{ $reviews->firstItem() ?? 0 }} to {{ $reviews->lastItem() ?? 0 }} of {{ $reviews->total() ?? 0 }} results
                </div>
                <div class="flex">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
