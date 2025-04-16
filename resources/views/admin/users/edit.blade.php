@extends('layouts.app')

@section('content')
    <div class="py-4">
        {{-- Header Section (no changes needed) --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Edit User: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

        {{-- Validation Errors (no changes needed) --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Validation Error</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 md:p-8"> {{-- Adjusted padding --}}
                <h2 class="text-xl font-medium mb-6">User Information</h2>

                {{-- Changed form action to the update route and added PUT method --}}
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Use PUT or PATCH for updates --}}

                    {{-- Common Fields (Mostly same, check 'role' id) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                   class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Enter full name" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                   class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Enter email address" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Leave blank to keep current password">
                            <p class="text-xs text-gray-500 mt-1">Only fill this if you want to change the password.</p>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            {{-- ** Important: Changed id to "role" to match JavaScript ** --}}
                            <select name="role" id="role"
                                    class="w-full border @error('role') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required>
                                <option value="">Select a Role</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="service_buyer" {{ old('role', $user->role) == 'service_buyer' ? 'selected' : '' }}>Service Buyer</option>
                                <option value="service_provider" {{ old('role', $user->role) == 'service_provider' ? 'selected' : '' }}>Service Provider</option>
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            {{-- Display current photo if available - check user schema for correct field name --}}
                            @if($user->profile_image) {{-- Assuming 'profile_image' based on create form --}}
                            <div class="mb-2">
                                {{-- Use asset() helper for public disk or Storage::url() --}}
                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Current Profile Photo" class="h-20 w-20 rounded-full object-cover border">
                            </div>
                            @else
                                <div class="mb-2">
                                    <span class="text-xs text-gray-500">No current photo.</span>
                                </div>
                            @endif
                            {{-- Changed name to profile_image to match create form --}}
                            <input type="file" name="profile_image" id="profile_image"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Optional. Upload new to replace. Max 2MB.</p>
                            @error('profile_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- START: Added Conditional Fields Container --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">

                        {{-- Service Buyer Fields --}}
                        {{-- Use nullsafe operator `?->` in case relationship is null --}}
                        <div id="serviceBuyerFields" style="display: none;">
                            <h3 class="text-lg font-medium mb-4 text-gray-800">Service Buyer Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="buyer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="buyer_phone" id="buyer_phone" value="{{ old('buyer_phone', $user->serviceBuyer?->phone) }}"
                                           class="w-full border @error('buyer_phone') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter phone number">
                                    @error('buyer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="buyer_location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="buyer_location" id="buyer_location" value="{{ old('buyer_location', $user->serviceBuyer?->location) }}"
                                           class="w-full border @error('buyer_location') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="e.g., City, Country">
                                    @error('buyer_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Service Provider Fields --}}
                        {{-- Use nullsafe operator `?->` in case relationship is null --}}
                        <div id="serviceProviderFields" style="display: none;">
                            <h3 class="text-lg font-medium mb-4 text-gray-800">Service Provider Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="provider_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="provider_phone" id="provider_phone" value="{{ old('provider_phone', $user->serviceProvider?->phone) }}"
                                           class="w-full border @error('provider_phone') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter phone number">
                                    @error('provider_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="provider_location" class="block text-sm font-medium text-gray-700 mb-1">Primary Location</label>
                                    <input type="text" name="provider_location" id="provider_location" value="{{ old('provider_location', $user->serviceProvider?->location) }}"
                                           class="w-full border @error('provider_location') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="e.g., City, Country">
                                    @error('provider_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="provider_type" class="block text-sm font-medium text-gray-700 mb-1">Provider Type</label>
                                    <select name="provider_type" id="provider_type"
                                            class="w-full border @error('provider_type') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select type</option>
                                        <option value="handyman" {{ old('provider_type', $user->serviceProvider?->provider_type) == 'handyman' ? 'selected' : '' }}>Handyman</option>
                                        <option value="bussiness_owner" {{ old('provider_type', $user->serviceProvider?->provider_type) == 'bussiness_owner' ? 'selected' : '' }}>Bussiness Owner</option>
                                    </select>
                                    @error('provider_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name (Optional)</label>
                                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $user->serviceProvider?->business_name) }}"
                                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter business name">
                                    @error('business_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mb-6">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio (Optional)</label>
                                <textarea name="bio" id="bio" rows="3"
                                          class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Brief description about the provider or business">{{ old('bio', $user->serviceProvider?->bio) }}</textarea>
                                @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="mb-6">
                                <label for="business_address" class="block text-sm font-medium text-gray-700 mb-1">Business Address (Optional)</label>
                                <input type="text" name="business_address" id="business_address" value="{{ old('business_address', $user->serviceProvider?->business_address) }}"
                                       class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       placeholder="Enter full business address">
                                @error('business_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                    {{-- END: Added Conditional Fields Container --}}

                    {{-- Form Actions (no changes needed) --}}
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200"> {{-- Added top border --}}
                        <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- START: Added JavaScript --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ** Important: Ensure the role select ID is "role" **
            const roleSelect = document.getElementById('role');
            const serviceBuyerFields = document.getElementById('serviceBuyerFields');
            const serviceProviderFields = document.getElementById('serviceProviderFields');

            // Check if elements exist before adding listeners
            if (roleSelect && serviceBuyerFields && serviceProviderFields) {
                function toggleRoleFields() {
                    const selectedRole = roleSelect.value;

                    // Hide both sections initially
                    serviceBuyerFields.style.display = 'none';
                    serviceProviderFields.style.display = 'none';

                    // Show the relevant section based on selection
                    if (selectedRole === 'service_buyer') {
                        serviceBuyerFields.style.display = 'block';
                    } else if (selectedRole === 'service_provider') {
                        serviceProviderFields.style.display = 'block';
                    }
                }

                // Add event listener for changes
                roleSelect.addEventListener('change', toggleRoleFields);

                // Run on page load to show fields based on the pre-selected role
                toggleRoleFields();
            } else {
                console.error("Role select or conditional field containers not found. Check element IDs.");
            }
        });
    </script>
@endpush
{{-- END: Added JavaScript --}}
