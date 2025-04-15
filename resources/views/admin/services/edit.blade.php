@extends('layouts.app')

@section('content')
    <div class="py-8 px-4 mx-auto max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Service</h1>
            <a href="{{ route('admin.services.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Services
            </a>
        </div>

        <!-- Notifications -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800">Service Information</h2>
            </div>

            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Service Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Service Title <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $service->title) }}" required
                               class="border border-gray-300 rounded p-2 w-full shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Enter service title">
                        @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Provider -->
                    <div>
                        <label for="provider_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Service Provider <span class="text-red-600">*</span>
                        </label>
                        <select id="provider_id" name="provider_id" required
                                class="w-full border border-gray-300 rounded p-2 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Select a Provider</option>
                            {{-- Iterate using key => value syntax --}}
                            @foreach($providers as $provider_user_id => $provider_display_name)
                                {{-- Use the key ($provider_user_id) for the value and comparison --}}
                                {{-- Use the value ($provider_display_name) for the displayed text --}}
                                <option value="{{ $provider_user_id }}" {{ old('provider_id', $service->provider_id) == $provider_user_id ? 'selected' : '' }}>
                                    {{ $provider_display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('provider_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-600">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="border border-gray-300 rounded p-2 w-full shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                              placeholder="Describe the service in detail">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Price <span class="text-red-600">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">$</span>
                            <input type="number" id="price" name="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" required
                                   class="w-full border p-2 rounded-none rounded-r-md border-gray-300 flex-1 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   placeholder="0.00">
                        </div>
                        @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Category <span class="text-red-600">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full border border-gray-300 rounded p-2 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Select a Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                            Location
                        </label>
                        <input type="text" id="location" name="location" value="{{ old('location', $service->location) }}"
                               class="border border-gray-300 rounded p-2 w-full shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               placeholder="Service location">
                        @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Type -->
                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Service Type
                        </label>
                        <select id="service_type" name="service_type"
                                class="w-full border border-gray-300 rounded p-2 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Select Type</option>
                            <option value="in-person" {{ old('service_type', $service->service_type) == 'in-person' ? 'selected' : '' }}>In-Person</option>
                            <option value="remote" {{ old('service_type', $service->service_type) == 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="hybrid" {{ old('service_type', $service->service_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('service_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full border border-gray-300 rounded p-2 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ old('status', $service->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image Preview -->
                @if($service->image)
                    <div class="mb-4">
                        <p class="block text-sm font-medium text-gray-700 mb-1">Current Image</p>
                        <div class="mt-1">
                            <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" class="h-32 w-auto object-cover rounded-md">
                        </div>
                    </div>
                @endif

                <!-- Image Upload -->
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1">
                        Upload New Service Image(s) {{-- Changed label slightly --}}
                    </label>
                    {{-- Changed id and name, added multiple --}}
                    <input type="file" id="images" name="images[]" multiple
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-md p-1">
                    <p class="mt-1 text-xs text-gray-500">You can upload multiple images. Max 2MB each (JPG, PNG, GIF).</p>
                    {{-- Display validation error for the images array --}}
                    @error('images')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    {{-- Display validation errors for individual files within the array --}}
                    @foreach ($errors->get('images.*') as $message)
                        <p class="mt-1 text-sm text-red-600">{{ $message[0] }}</p>
                    @endforeach
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


