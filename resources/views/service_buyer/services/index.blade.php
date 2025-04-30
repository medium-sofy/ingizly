@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen">
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 flex items-center text-gray-800 dark:text-gray-100">
        <i class="fas fa-search text-blue-500 mr-3"></i> Browse Services
    </h2>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 sm:p-6 mb-6">
        <form action="{{ route('buyer.services.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="Search services...">
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select name="category" id="category" 
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Type</label>
                <select name="service_type" id="service_type" 
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    <option value="all">All Types</option>
                    <option value="on_site" {{ request('service_type') == 'on_site' ? 'selected' : '' }}>On Site</option>
                    <option value="remote" {{ request('service_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="business_based" {{ request('service_type') == 'business_based' ? 'selected' : '' }}>Business Based</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($services as $service)
            <a href="{{ route('service.details', $service->id) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="h-48 overflow-hidden">
                    @if($service->images->isNotEmpty())
                        <img src="{{ Storage::url($service->images->first()->image_url) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 dark:text-gray-500 text-4xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1 truncate">{{ $service->title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <i class="fas fa-tag mr-1"></i> {{ $service->category->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <i class="fas fa-user mr-1"></i> {{ $service->provider->user->name }}
                    </p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $service->price }} EGP</span>
                        <span class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-500 text-sm font-medium">
                            View Details
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                <i class="fas fa-search text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">No services found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $services->withQueryString()->links() }}
    </div>
</div>
@endsection