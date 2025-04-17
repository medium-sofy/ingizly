@extends('layouts.provider')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-4">{{ $service->title }}</h1>

    <p><strong>Category:</strong> {{ $service->category->name }}</p>
    <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</p>
    <p><strong>Location:</strong> {{ $service->location ?? 'N/A' }}</p>
    <p><strong>Price:</strong> EGP {{ $service->price }}</p>
    <p class="mt-4"><strong>Description:</strong></p>
    <p>{{ $service->description }}</p>

    @if ($service->images->count())
        <div class="mt-4">
            <strong>Image:</strong>
            <img src="{{ asset('storage/' . $service->images->first()->image_url) }}" alt="Service Image" class="mt-2 w-64 h-64 object-cover rounded shadow">
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('services.edit', $service) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
        <a href="{{ route('services.index') }}" class="ml-2 text-gray-500">Back</a>
    </div>
</div>
@endsection
