@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6"> {{-- Added wrap and gap --}}
            <h1 class="text-3xl font-bold">Add New User</h1>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded inline-flex items-center whitespace-nowrap"> {{-- Added inline-flex --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

        {{-- Display General Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 md:p-8"> {{-- Added more padding on medium screens --}}
                <h2 class="text-xl font-medium mb-6">User Information</h2>

                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Common Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Enter full name" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Enter email address" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password"
                                   class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Enter password" required>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Confirm password" required>
                            {{-- Password confirmation errors usually shown under 'password' field by Laravel --}}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="role"
                                    class="w-full border @error('role') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required>
                                <option value="">Select a Role</option>
                                {{-- Use old('role') to re-select after validation error --}}
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="service_buyer" {{ old('role') == 'service_buyer' ? 'selected' : '' }}>Service Buyer</option>
                                <option value="service_provider" {{ old('role') == 'service_provider' ? 'selected' : '' }}>Service Provider</option>
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            {{-- Changed name to profile_image to match controller/schema --}}
                            <input type="file" name="profile_image" id="profile_image"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded cursor-pointer"
                            >
                            <p class="text-xs text-gray-500 mt-1">Optional. Max 2MB (JPG, PNG, GIF)</p>
                            @error('profile_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Conditional Fields Container --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">

                        <div id="serviceBuyerFields" style="display: none;"> {{-- Hidden by default --}}
                            <h3 class="text-lg font-medium mb-4 text-gray-800">Service Buyer Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="buyer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <input type="tel" name="buyer_phone" id="buyer_phone" value="{{ old('buyer_phone') }}"
                                           class="w-full border @error('buyer_phone') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter phone number">
                                    @error('buyer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="buyer_location" class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                                    <input type="text" name="buyer_location" id="buyer_location" value="{{ old('buyer_location') }}"
                                           class="w-full border @error('buyer_location') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="e.g., City, Country">
                                    @error('buyer_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div id="serviceProviderFields" style="display: none;"> {{-- Hidden by default --}}
                            <h3 class="text-lg font-medium mb-4 text-gray-800">Service Provider Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="provider_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <input type="tel" name="provider_phone" id="provider_phone" value="{{ old('provider_phone') }}"
                                           class="w-full border @error('provider_phone') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter phone number">
                                    @error('provider_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="provider_location" class="block text-sm font-medium text-gray-700 mb-1">Primary Location <span class="text-red-500">*</span></label>
                                    <input type="text" name="provider_location" id="provider_location" value="{{ old('provider_location') }}"
                                           class="w-full border @error('provider_location') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="e.g., City, Country">
                                    @error('provider_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="provider_type" class="block text-sm font-medium text-gray-700 mb-1">Provider Type <span class="text-red-500">*</span></label>
                                    <select name="provider_type" id="provider_type"
                                            class="w-full border @error('provider_type') border-red-500 @else border-gray-300 @enderror rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select type</option>
                                        <option value="handyman" {{ old('provider_type') == 'handyman' ? 'selected' : '' }}>Handyman</option>
                                        <option value="shop_owner" {{ old('provider_type') == 'shop_owner' ? 'selected' : '' }}>Shop Owner</option>
                                    </select>
                                    @error('provider_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name (Optional)</label>
                                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}"
                                           class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter business name">
                                    @error('business_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mb-6">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio (Optional)</label>
                                <textarea name="bio" id="bio" rows="3"
                                          class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Brief description about the provider or business">{{ old('bio') }}</textarea>
                                @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="mb-6">
                                <label for="business_address" class="block text-sm font-medium text-gray-700 mb-1">Business Address (Optional)</label>
                                <input type="text" name="business_address" id="business_address" value="{{ old('business_address') }}"
                                       class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       placeholder="Enter full business address">
                                @error('business_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div> {{-- End Conditional Fields Container --}}


                    <div class="flex justify-end space-x-3 mt-8">
                        <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const serviceBuyerFields = document.getElementById('serviceBuyerFields');
            const serviceProviderFields = document.getElementById('serviceProviderFields');

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

            // Run on page load to show fields if validation failed and role was pre-selected
            toggleRoleFields();
        });
    </script>
@endpush
