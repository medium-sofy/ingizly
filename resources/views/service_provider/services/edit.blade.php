@extends('layouts.provider')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Service</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 border border-red-300 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" id="title"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ old('title', $service->title) }}" required>
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category_id" id="category_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $service->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (EGP)</label>
                <input type="number" name="price" id="price"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ old('price', $service->price) }}" required>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" id="location"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="{{ old('location', $service->location) }}">
            </div>
        </div>

        <div>
            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
            <select name="service_type" id="service_type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
                <option value="">Select Type</option>
                <option value="on_site" {{ $service->service_type == 'on_site' ? 'selected' : '' }}>On Site</option>
                <option value="remote" {{ $service->service_type == 'remote' ? 'selected' : '' }}>Remote</option>
                <option value="bussiness_based" {{ $service->service_type == 'bussiness_based' ? 'selected' : '' }}>Business Based</option>
            </select>
        </div>

        <div class="flex justify-between items-center mt-6">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Update Service
            </button>

            <a href="{{ route('services.index') }}"
                class="text-gray-500 hover:text-blue-600 underline text-sm transition duration-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
