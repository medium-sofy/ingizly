@extends('layouts.provider')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6 bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-600 pb-2">
        Edit Service
    </h2>

    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-200/10 text-red-700 dark:text-red-300 border border-red-300 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('provider.services.update', $service->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
            <input type="text" name="title" id="title"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                value="{{ old('title', $service->title) }}" required>
        </div>

        {{-- Category --}}
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <select name="category_id" id="category_id"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $service->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
            <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                required>{{ old('description', $service->description) }}</textarea>
        </div>

        {{-- Price & Location --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price (EGP)</label>
                <input type="number" name="price" id="price"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                    value="{{ old('price', $service->price) }}" required>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                <input type="text" name="location" id="location"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                    value="{{ old('location', $service->location) }}">
            </div>
        </div>

        {{-- Service Type --}}
        <div>
            <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Type</label>
            <select name="service_type" id="service_type"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-black dark:text-white"
                required>
                <option value="">Select Type</option>
                <option value="on_site" {{ $service->service_type == 'on_site' ? 'selected' : '' }}>On Site</option>
                <option value="remote" {{ $service->service_type == 'remote' ? 'selected' : '' }}>Remote</option>
                <option value="bussiness_based" {{ $service->service_type == 'bussiness_based' ? 'selected' : '' }}>Business Based</option>
            </select>
        </div>

        {{-- Existing Images --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Existing Images</label>
            <div class="flex flex-wrap gap-4">
                @foreach ($service->images as $image)
                    <div class="relative w-32 h-32 border border-gray-300 dark:border-gray-600 rounded overflow-hidden">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Service Image"
                             class="w-full h-full object-cover rounded">
                        <button type="button"
                                onclick="deleteImage({{ $image->id }})"
                                class="absolute top-1 right-1 bg-red-600 text-white text-xs rounded-full px-2 py-1 hover:bg-red-700">
                            ✕
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Upload New Images --}}
        <div class="mb-6">
            <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add New Images</label>
            <input type="file" name="images[]" id="images" multiple accept="image/*"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 shadow-sm focus:ring focus:ring-indigo-200 bg-white dark:bg-gray-700 text-black dark:text-white">
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-between items-center mt-6">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Update Service
            </button>
            <a href="{{ route('provider.services.index') }}"
                class="text-gray-500 dark:text-gray-300 hover:text-blue-600 underline text-sm transition duration-200">Cancel</a>
        </div>
    </form>
</div>

{{-- JavaScript Delete Form --}}
<script>
    function deleteImage(imageId) {
        if (confirm('Are you sure you want to delete this image?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/provider/services/image/${imageId}`;
            form.style.display = 'none';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
