@extends('layouts.provider')

@section('content')
<!-- Alpine.js for modal -->
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="{ showModal: false, serviceId: null }" class="p-6 bg-gray-100 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-3xl font-semibold text-gray-800">My Services</h2>
        <a href="{{ route('services.create') }}" class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full shadow transition duration-300">
            + Add New Service
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-300 rounded-lg shadow-sm">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($services->isEmpty())
        <div class="text-gray-500 text-center py-10">You have no services listed.</div>
    @else
        <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $service)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition border border-gray-100">
                    <!-- Service clickable area -->
                    <a href="{{ route('services.show', $service->id) }}" class="block p-5">
                        <h3 class="text-xl font-semibold text-gray-800 mb-1 truncate">{{ $service->title }}</h3>
                        <p class="text-sm text-gray-500 mb-1">
                            Category: <span class="font-medium">{{ $service->category->name ?? 'N/A' }}</span>
                        </p>
                        <p class="text-sm text-gray-500 mb-1">
                            Location: <span class="font-medium">{{ $service->location ?? 'Not provided' }}</span>
                        </p>
                        <p class="text-gray-700 mb-3"><strong>{{ $service->price }} EGP</strong></p>

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs px-3 py-1 rounded-full {{ $service->status == 'active' ? 'bg-green-500' : 'bg-yellow-500' }} text-white">
                                {{ ucfirst($service->status) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $service->view_count }} views</span>
                        </div>
                    </a>

                    <!-- Buttons outside the link -->
                    <div class="flex justify-end gap-2 px-5 pb-4">
                        <a href="{{ route('services.edit', $service->id) }}" class="inline-flex items-center gap-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium px-3 py-1.5 rounded-full transition">
                            ✏️ Edit
                        </a>
                        <button
                            @click.prevent="showModal = true; serviceId = {{ $service->id }};"
                            class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-600 text-sm font-medium px-3 py-1.5 rounded-full transition">
                            🗑️ Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <form id="deleteForm" method="POST"
          x-show="showModal"
          :action="'/services/' + serviceId"
          class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
    >
        @csrf
        @method('DELETE')
        <div @click.outside="showModal = false" class="bg-white rounded-xl p-6 shadow-lg max-w-sm w-full">
            <div class="flex flex-col items-center text-center">
                <div class="bg-red-100 p-3 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Confirm Delete</h2>
                <p class="text-sm text-gray-600 mb-5">Are you sure you want to delete this service? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-800">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
