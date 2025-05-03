@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Categories Management</h1>
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add New Category
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.categories.index') }}" method="GET">
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" name="search" placeholder="Search categories..."
                           value="{{ request('search') }}"
                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-wrap items-center gap-4 mb-4">


                    <!-- Apply Filters Button -->
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 text-left font-medium text-gray-700">Name</th>
                        <th class="py-3 text-left font-medium text-gray-700">Parent Category</th>
                        <th class="py-3 text-left font-medium text-gray-700">Services Count</th>
                        <th class="py-3 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <div class="flex items-center">
                                    @if($category->icon)

                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $category->name }}</p>
                                        @if($category->children->count() > 0)
                                            <p class="text-sm text-gray-500">
                                                {{ $category->children->count() }} subcategories
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                {{ $category->parent ? $category->parent->name : 'None' }}
                            </td>


                            <td class="py-4">
                                {{ $category->services_count ?? 0 }}
                            </td>
                            <td class="py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.categories.show', $category->id) }}"
                                       class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                                        View
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                       class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded"
                                                onclick="return confirm('Are you sure you want to delete this category?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @error('parent_id')
                        @enderror
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">No categories found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() ?? 0 }} results
                </div>
                <div class="flex">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
