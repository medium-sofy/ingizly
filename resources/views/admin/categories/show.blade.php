@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Category Details: {{ $category->name }}</h1>
            <div class="flex space-x-4">
                <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-500 hover:text-blue-600">
                    Edit Category
                </a>
                <a href="{{ route('admin.categories.index') }}" class="text-blue-500 hover:text-blue-600">
                    Back to Categories
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Category Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $category->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Parent Category</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $category->parent ? $category->parent->name : 'None (Top Level Category)' }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $category->description }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Icon</label>
                            @if($category->icon)
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }} icon"
                                     class="mt-2 w-16 h-16 object-cover rounded">
                            @else
                                <p class="mt-1 text-sm text-gray-500">No icon uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Related Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Related Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subcategories</label>
                            @if($category->children->count() > 0)
                                <ul class="mt-1 list-disc list-inside text-sm text-gray-900">
                                    @foreach($category->children as $child)
                                        <li>{{ $child->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-1 text-sm text-gray-500">No subcategories</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Services in this Category</label>
                            @if($category->services->count() > 0)
                                <ul class="mt-1 list-disc list-inside text-sm text-gray-900">
                                    @foreach($category->services as $service)
                                        <li>{{ $service->title }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-1 text-sm text-gray-500">No services in this category</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 