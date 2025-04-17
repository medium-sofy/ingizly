@extends('layouts.app')

@section('content')
    <div class="py-8 px-4 mx-auto max-w-6xl">
        {{-- Header --}}
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 break-words">
                Service Details: {{ $service->title }}
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.services.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Services
                </a>
                {{-- Edit Button --}}
                <a href="{{ route('admin.services.edit', $service->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit
                </a>
                {{-- Delete Button (Optional - requires JavaScript or separate form) --}}
                <form id="delete-form-{{ $service->id }}" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete({{ $service->id }})" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800">Service Information</h2>
            </div>

            <div class="p-6 space-y-6"> {{-- Use space-y for vertical spacing --}}

                {{-- Row 1 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Service Title</label>
                        <p class="text-gray-900">{{ $service->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Service Provider</label>
                        {{-- Use nullsafe operator '?->' --}}
                        <p class="text-gray-900">{{ $service->provider?->user?->name ?? 'N/A' }}
                            @if($service->provider?->business_name)
                                <span class="text-gray-600">({{ $service->provider->business_name }})</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">{{ $service->provider?->location ?? 'No location specified' }}</p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $service->description }}</p> {{-- Use whitespace-pre-wrap to respect formatting --}}
                </div>

                {{-- Row 2 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Price</label>
                        <p class="text-gray-900">${{ number_format($service->price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        {{-- Use nullsafe operator '?->' --}}
                        <p class="text-gray-900">{{ $service->category?->name ?? 'No Category' }}</p>
                    </div>
                </div>

                {{-- Row 3 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Location (Service Specific)</label>
                        <p class="text-gray-900">{{ $service->location ?: ($service->provider?->location ?: 'N/A') }}</p> {{-- Fallback to provider location if service location is empty --}}
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Service Type</label>
                        <p class="text-gray-900">{{ Str::ucfirst(str_replace('_', '-', $service->service_type ?? 'N/A')) }}</p>
                    </div>
                </div>

                {{-- Row 4 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full {{
                            match ($service->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'inactive' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            }
                        }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Views</label>
                        <p class="text-gray-900">{{ number_format($service->view_count ?? 0) }}</p>
                    </div>
                    {{-- You might want to add Rating here if applicable --}}
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Average Rating</label>
                        <p class="text-gray-900">{{ number_format($service->provider?->avg_rating ?? 0, 1) }}/5</p>
                    </div> --}}
                </div>

                {{-- Service Images --}}
                <div class="pt-6 border-t border-gray-200">
                    <label class="block text-lg font-medium text-gray-800 mb-4">Service Images</label>
                    @if($service->images && $service->images->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($service->images as $image)
                                <div class="relative border rounded-md overflow-hidden {{ $image->is_primary ? 'border-2 border-blue-500' : 'border-gray-200' }}">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="Service Image {{ $loop->iteration }}" class="w-full h-32 object-cover">
                                    @if($image->is_primary)
                                        <span class="absolute top-1 right-1 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">Primary</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No images uploaded for this service.</p>
                    @endif
                </div>

                {{-- Add other sections if needed, e.g., Recent Orders, Reviews for this service --}}

            </div>
        </div>
    </div>

    {{-- JavaScript for Delete Confirmation --}}
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this service and its associated images? This action cannot be undone.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
