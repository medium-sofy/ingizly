@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100">
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 flex items-center">
        <i class="fas fa-search text-green-500 mr-3"></i> Browse Services
    </h2>

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <form action="{{ route('buyer.services.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                    placeholder="Search services...">
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" 
                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <select name="service_type" id="service_type" 
                    class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500">
                    <option value="all">All Types</option>
                    <option value="on_site" {{ request('service_type') == 'on_site' ? 'selected' : '' }}>On Site</option>
                    <option value="remote" {{ request('service_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="business_based" {{ request('service_type') == 'business_based' ? 'selected' : '' }}>Business Based</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($services as $service)
            <div class="bg-white rounded-lg shadow overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="h-48 overflow-hidden">
                    @if($service->images->isNotEmpty())
                        <img src="{{ Storage::url($service->images->first()->image_url) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1 truncate">{{ $service->title }}</h3>
                    <p class="text-sm text-gray-500 mb-2">
                        <i class="fas fa-tag mr-1"></i> {{ $service->category->name }}
                    </p>
                    <p class="text-sm text-gray-500 mb-2">
                        <i class="fas fa-user mr-1"></i> {{ $service->provider->user->name }}
                    </p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-green-600 font-bold">{{ $service->price }} EGP</span>
                        <a href="{{ route('buyer.services.show', $service->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-search text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">No services found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $services->withQueryString()->links() }}
    </div>
</div>
@endsection