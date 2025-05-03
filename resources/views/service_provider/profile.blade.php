@extends('layouts.provider')

@section('content')
<div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
    <!-- Title Section -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">Your Profile</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Manage your account details, update your password, or delete your account.</p>
    </div>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <!-- Profile Content -->
        <div class="p-6 space-y-8">
            <!-- Profile Form -->
            <form method="POST" action="{{ route('service_provider.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Profile Image -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <img id="profileImagePreview" src="{{ auth()->user()->profile_image ? Storage::url(auth()->user()->profile_image) : asset('default-profile.png') }}" 
                            alt="Profile Image" class="w-24 h-24 rounded-full object-cover border-4 border-blue-500 shadow-lg">
                        <label for="profile_image" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*" onchange="previewProfileImage(event)">
                    </div>
                    @error('profile_image') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Fields -->
                @php
                    $fields = [
                        'name' => 'user',
                        'email' => 'envelope',
                        'phone_number' => 'phone',
                        'bio' => 'info-circle',
                        'location' => 'map-marker-alt',
                        'business_name' => 'briefcase',
                        'business_address' => 'map'
                    ];
                @endphp

                @foreach ($fields as $field => $icon)
                <div>
                    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        <i class="fas fa-{{ $icon }} mr-2"></i>{{ ucfirst(str_replace('_', ' ', $field)) }}
                    </label>
                    @if ($field === 'bio')
                        <textarea id="{{ $field }}" name="{{ $field }}" rows="4"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old($field, auth()->user()->serviceProvider->$field) }}</textarea>
                    @else
                        <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, auth()->user()->serviceProvider->$field ?? auth()->user()->$field) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @endif
                    @error($field)
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>
                @endforeach

                <!-- Submit Button -->
                <div class="text-left">
                    <button type="submit" class="w-auto px-6 bg-blue-500 text-white py-1.5 rounded-md font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        <i class="fas fa-save mr-2"></i>Update Profile
                    </button>
                </div>
            </form>

            <!-- Update Password -->
            <form method="POST" action="{{ route('service_provider.profile.update_password') }}" class="space-y-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Update Password</h2>

                @foreach (['current_password' => 'lock', 'new_password' => 'key', 'new_password_confirmation' => 'check-circle'] as $field => $icon)
                <div>
                    <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        <i class="fas fa-{{ $icon }} mr-2"></i>{{ ucwords(str_replace('_', ' ', $field)) }}
                    </label>
                    <input type="password" id="{{ $field }}" name="{{ $field }}" 
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error($field) 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>
                @endforeach

                <!-- Submit Button -->
                <div class="text-left">
                    <button type="submit" class="w-auto px-6 bg-green-500 text-white py-1.5 rounded-md font-medium hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Update Password
                    </button>
                </div>
            </form>

            <!-- Delete Account -->
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold mb-4 text-red-600">Delete Account</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                <div class="text-left">
                    <button type="button" onclick="showDeleteModal()" class="w-auto px-6 bg-red-500 text-white py-1.5 rounded-md font-medium hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                        <i class="fas fa-trash-alt mr-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold text-red-600 mb-4">Are you sure?</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6">This action cannot be undone. Do you really want to delete your account?</p>
        <div class="flex justify-end space-x-4">
            <button onclick="hideDeleteModal()" class="px-4 py-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 focus:outline-none">
                Cancel
            </button>
            <form method="POST" action="{{ route('service_provider.profile.delete') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function previewProfileImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('profileImagePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function showDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection
