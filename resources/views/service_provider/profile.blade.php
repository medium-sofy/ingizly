@extends('layouts.provider')

@section('content')
<div class="p-6 bg-gray-100 min-h-screen">
    <!-- Title Section -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600">Your Profile</h1>
        <p class="text-sm text-gray-600 mt-1">Manage your account details, update your password, or delete your account.</p>
    </div>

    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
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

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user mr-2"></i>Name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('email') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-phone mr-2"></i>Phone Number
                    </label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', auth()->user()->serviceProvider->phone_number) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('phone_number') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-info-circle mr-2"></i>Bio
                    </label>
                    <textarea id="bio" name="bio" rows="4" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('bio', auth()->user()->serviceProvider->bio) }}</textarea>
                    @error('bio') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-map-marker-alt mr-2"></i>Location
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location', auth()->user()->serviceProvider->location) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('location') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-briefcase mr-2"></i>Business Name
                    </label>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name', auth()->user()->serviceProvider->business_name) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('business_name') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Business Address -->
                <div>
                    <label for="business_address" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-map mr-2"></i>Business Address
                    </label>
                    <input type="text" id="business_address" name="business_address" value="{{ old('business_address', auth()->user()->serviceProvider->business_address) }}" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('business_address') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="text-left">
                    <button type="submit" class="w-auto px-6 bg-blue-500 text-white py-1.5 rounded-md font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        <i class="fas fa-save mr-2"></i>Update Profile
                    </button>
                </div>
            </form>

            <!-- Update Password -->
            <form method="POST" action="{{ route('service_provider.profile.update_password') }}" class="space-y-6 bg-gray-50 p-6 rounded-lg shadow">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-bold mb-4">Update Password</h2>

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2"></i>Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('current_password') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-key mr-2"></i>New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('new_password') 
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-check-circle mr-2"></i>Confirm New Password
                    </label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Submit Button -->
                <div class="text-left">
                    <button type="submit" class="w-auto px-6 bg-green-500 text-white py-1.5 rounded-md font-medium hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Update Password
                    </button>
                </div>
            </form>

            <!-- Delete Account -->
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold mb-4 text-red-600">Delete Account</h2>
                <p class="text-gray-600 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
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
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-lg font-bold text-red-600 mb-4">Are you sure?</h2>
        <p class="text-gray-600 mb-6">This action cannot be undone. Do you really want to delete your account?</p>
        <div class="flex justify-end space-x-4">
            <button onclick="hideDeleteModal()" class="px-4 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none">
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