@extends('layouts.provider')

@section('content')
<div class="max-w-4xl mx-auto p-6 sm:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md mt-6">
    <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-6 flex items-center gap-3">
        <i class="fas fa-plus-circle text-blue-500 text-2xl"></i>
        Add New Service
    </h2>

    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-200/10 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('provider.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">
                    <option value="">Select a Category</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Type</label>
                <select name="service_type" id="service_type" required
                        class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">
                    <option value="">Select Type</option>
                    <option value="on_site" {{ old('service_type') == 'on_site' ? 'selected' : '' }}>On Site</option>
                    <option value="remote" {{ old('service_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                    <option value="bussiness_based" {{ old('service_type') == 'bussiness_based' ? 'selected' : '' }}>Business Based</option>
                </select>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price (EGP)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
            <textarea name="description" id="description" rows="4" required
                      class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
            <input type="text" name="location" id="location" value="{{ old('location') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white">
        </div>

        <div>
            <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Images</label>
            <input type="file" name="images[]" id="images" multiple accept="image/*"
                   class="w-full border border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500 rounded-lg shadow-sm px-4 py-2 transition bg-white dark:bg-gray-700 text-black dark:text-white"
                   onchange="previewImages(event)">
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">You can upload multiple images (Max 2MB each).</p>

            <div id="preview-container" class="mt-4 flex flex-wrap gap-4 justify-center"></div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
            <a href="{{ route('provider.services.index') }}"
               class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                Cancel
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-blue-700 transition shadow">
                <i class="fas fa-check-circle mr-1"></i> Create Service
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let selectedFiles = [];

    function previewImages(event) {
        const input = event.target;
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        selectedFiles = Array.from(input.files);

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('relative', 'inline-block');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('w-32', 'h-32', 'object-cover', 'rounded', 'border', 'border-gray-300', 'dark:border-gray-600');

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.classList.add('absolute', '-top-2', '-right-2', 'bg-red-500', 'text-white', 'rounded-full', 'w-6', 'h-6', 'text-xs', 'flex', 'items-center', 'justify-center', 'shadow-md');
                removeBtn.addEventListener('click', () => removeImage(index));

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        updateFileInput();
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
        updateFileInput();
    }

    function renderPreviews() {
        const previewContainer = document.getElementById('preview-container');
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('relative', 'inline-block');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('w-32', 'h-32', 'object-cover', 'rounded', 'border', 'border-gray-300', 'dark:border-gray-600');

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.classList.add('absolute', '-top-2', '-right-2', 'bg-red-500', 'text-white', 'rounded-full', 'w-6', 'h-6', 'text-xs', 'flex', 'items-center', 'justify-center', 'shadow-md');
                removeBtn.addEventListener('click', () => removeImage(index));

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('images').files = dataTransfer.files;
    }
</script>
@endpush
@endsection
