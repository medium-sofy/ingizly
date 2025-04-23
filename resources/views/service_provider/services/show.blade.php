@extends('layouts.provider')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6 bg-white shadow-md rounded-2xl">
    <div class="mb-8 border-b pb-6">
        <h1 class="text-4xl font-bold text-gray-800">{{ $service->title }}</h1>
        <p class="text-sm text-gray-500 mt-1">Created at: {{ $service->created_at->format('F j, Y') }}</p>
    </div>

    {{-- Service Info Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-gray-700 mb-10">
        <div>
            <h2 class="text-lg font-semibold mb-1">Category</h2>
            <p>{{ $service->category->name }}</p>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-1">Service Type</h2>
            <p>{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</p>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-1">Location</h2>
            <p>{{ $service->location ?? 'N/A' }}</p>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-1">Price</h2>
            <p>EGP {{ number_format($service->price, 2) }}</p>
        </div>
    </div>

    {{-- Description --}}
    <div class="mb-10">
        <h2 class="text-lg font-semibold mb-2 text-gray-800">Description</h2>
        <p class="text-gray-600 leading-relaxed">{{ $service->description }}</p>
    </div>

    {{-- Image Gallery --}}
    @if ($service->images->count())
        <div class="mb-10">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Service Images</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($service->images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Service Image"
                             class="w-full h-48 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('provider.services.edit', $service) }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition duration-200">
            Edit Service
        </a>
        <a href="{{ route('provider.services.index') }}"
           class="text-gray-500 hover:text-blue-600 text-sm underline">
            ← Back to Services
        </a>
    </div>
</div>
@endsection
