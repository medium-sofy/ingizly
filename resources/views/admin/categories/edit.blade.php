@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Edit Category: {{ $category->name }}</h1>
            <a href="{{ route('admin.categories.index') }}" class="text-blue-500 hover:text-blue-600">
                Back to Categories
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Category Field -->
                <div class="mb-6">
                    <label for="parent_category_id" class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
                    <select name="parent_category_id" id="parent_category_id"
                            class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('parent_category_id') border-red-500 @enderror">
                        <option value="">None (Top Level Category)</option>
                        @foreach($parentCategories as $cat)
                            @if($cat->id !== $category->id) {{-- Prevent self-parenting --}}
                                <option value="{{ $cat->id }}" {{ old('parent_category_id', $category->parent_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('parent_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description Field -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Icon Display -->
                @if($category->icon)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Icon</label>
                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }} icon"
                             class="w-16 h-16 object-cover rounded">
                    </div>
                @endif

                <!-- Icon Upload Field -->
                <div class="mb-6">
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">New Icon (Optional)</label>
                    <input type="file" name="icon" id="icon" accept="image/*"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('icon') border-red-500 @enderror">
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Upload a square icon image (recommended size: 128x128 pixels)</p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


