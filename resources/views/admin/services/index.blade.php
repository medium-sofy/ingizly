@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Services List</h1>
            <a href="{{ route('admin.services.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add New Service
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.services.index') }}" method="GET">
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" name="search" placeholder="Search services..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <!-- Price Range -->
                    <div class="flex items-center">
                        <input type="number" name="min_price" placeholder="Min"
                               value="{{ request('min_price') }}"
                               class="w-24 border border-gray-300 rounded p-2">
                        <span class="px-2">-</span>
                        <input type="number" name="max_price" placeholder="Max"
                               value="{{ request('max_price') }}"
                               class="w-24 border border-gray-300 rounded p-2">
                    </div>

                    <!-- Category Filter -->
                    <select name="category" class="border border-gray-300 rounded p-2">
                        <option value="All Categories">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Location Filter -->
                    <select name="location" class="border border-gray-300 rounded p-2">
                        <option value="All Locations">All Locations</option>
                        @foreach($locations as $location)
                            {{-- $location is the location string itself, e.g., "Faiyum" --}}
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="border border-gray-300 rounded p-2">
                        <option value="All Statuses">All Statuses</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <!-- Apply Filters Button -->
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Services Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-left font-medium text-gray-700">Service Details</th>
                        <th class="py-3 text-left font-medium text-gray-700">Provider</th>
                        <th class="py-3 text-left font-medium text-gray-700">Price</th>
                        <th class="py-3 text-left font-medium text-gray-700">Views</th>
                        <th class="py-3 text-left font-medium text-gray-700">Category</th>
                        <th class="py-3 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($services as $service)
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <div>
                                    <p class="font-medium">{{ $service->title }}</p>
                                    <p class="text-sm text-gray-500">{{ Str::limit($service->description, 50) }}</p>
                                    <span class="inline-block px-2 py-1 text-xs {{
                                            $service->status == 'active' ? 'bg-green-100 text-green-800' :
                                            ($service->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                        }} rounded-full mt-1">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                </div>
                            </td>
                            <td class="py-4">
                                <div>
                                    {{-- Use the correct relationship name 'provider' --}}
                                    {{-- Use nullsafe operator '?->' to prevent errors if provider or user is null --}}
                                    <p class="font-medium">{{ $service->provider?->user?->name ?? 'Provider missing' }}</p>
                                    <p class="text-sm text-gray-500">{{ $service->provider?->location ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="py-4">${{ number_format($service->price) }}</td>
                            <td class="py-4">{{ number_format($service->views) }}</td>
                            <td class="py-4">{{ $service->category->name ?? 'No Category' }}</td>
                            <td class="py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.services.show', $service->id) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                        View
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded">
                                        Edit
                                    </a>
                                    <button onclick="deleteService({{ $service->id }})" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">No services found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing {{ $services->firstItem() ?? 0 }} to {{ $services->lastItem() ?? 0 }} of {{ $services->total() ?? 0 }} results
                </div>
                <div class="flex">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>

    @foreach($services as $service)
        <form id="delete-form-{{ $service->id }}" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
