@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            {{-- Changed Title --}}
            <h1 class="text-3xl font-bold">Edit User: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

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
            <div class="p-6">
                <h2 class="text-xl font-medium mb-6">User Information</h2>

                {{-- Changed form action to the update route and added PUT method --}}
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Use PUT or PATCH for updates --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            {{-- Pre-filled value using old() helper and $user data --}}
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter full name" required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            {{-- Pre-filled value --}}
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter email address" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            {{-- Password is optional on edit, removed required --}}
                            <input type="password" name="password" id="password"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Leave blank to keep current password">
                            <p class="text-xs text-gray-500 mt-1">Only fill this if you want to change the password.</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            {{-- Password confirmation is also optional --}}
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="role_id"
                                    class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <option value="">Select a Role</option>
                                {{-- Pre-selected role based on $user->role --}}
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="service_buyer" {{ old('role', $user->role) == 'service_buyer' ? 'selected' : '' }}>
                                    Service Buyer
                                </option>
                                <option value="service_provider" {{ old('role', $user->role) == 'service_provider' ? 'selected' : '' }}>
                                    Service Provider
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            {{-- Display current photo if available --}}
                            @if($user->profile_photo_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Current Profile Photo" class="h-20 w-20 rounded-full object-cover">
                                    <p class="text-xs text-gray-500 mt-1">Current photo</p>
                                </div>
                            @endif
                            <input type="file" name="profile_photo" id="profile_photo"
                                   class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Upload a new photo to replace the current one. Recommended size: 200x200px (Max 2MB)</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8">
                        {{-- Changed Reset button to maybe a Cancel link? Or keep as Reset --}}
                        <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded">
                            Cancel
                        </a>
                        {{-- Changed Submit button text --}}
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
