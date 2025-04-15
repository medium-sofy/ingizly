@extends('layouts.provider')

@section('content')
<div class="max-w-4xl mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-md mt-6">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-6 flex items-center gap-3">
        <i class="fas fa-plus-circle text-blue-500 text-2xl"></i>
        Add New Service
    </h2>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('services.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Service Title</label>
            <input type="text" name="title" id="title"
                   class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                   value="{{ old('title') }}" required>
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category_id" id="category_id"
                    class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                    required>
                <option value="">Select a Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
            <select name="service_type" id="service_type"
                    class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                    required>
                <option value="on_site" {{ old('service_type') == 'on_site' ? 'selected' : '' }}>On Site</option>
                <option value="remote" {{ old('service_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                <option value="bussiness_based" {{ old('service_type') == 'bussiness_based' ? 'selected' : '' }}>Business Based</option>
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                      required>{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (EGP)</label>
                <input type="number" name="price" id="price"
                       class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                       value="{{ old('price') }}" required>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" id="location"
                       class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
                       value="{{ old('location') }}">
            </div>
        </div>


        <div>
    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Service Image</label>
    <input type="file" name="image" id="image" accept="image/*"
           class="w-full border border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition"
           onchange="previewImage(event)">
           <div id="preview-container" class="mt-4 border rounded p-2">
    <img id="image-preview" src="#" alt="Image Preview" class="hidden w-32 h-32 object-cover rounded">
</div>
</div>


        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('services.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 transition">Cancel</a>
            <button type="submit"
                    class="bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-700 transition shadow">
                <i class="fas fa-check-circle mr-1"></i> Create Service
            </button>
        </div>
    </form>
</div>
@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

@endsection
