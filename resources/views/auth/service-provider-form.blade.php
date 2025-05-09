@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Title Section -->
    <div class="text-center mb-8">
        <h2 class="text-4xl font-extrabold text-blue-600">Complete Your Service Provider Profile</h2>
        <p class="text-lg text-gray-500 mt-2">Tell us more about your business to get started</p>
    </div>

    <!-- Form Container -->
    <div class="max-w-2xl w-full bg-white shadow-2xl rounded-lg overflow-hidden">
        <!-- Form -->
        <div class="p-8">
            <form method="POST" action="{{ route('service_provider.store') }}" class="space-y-6">
                @csrf

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone_number') border-red-500 @enderror" 
                        value="{{ old('phone_number') }}" required>
                    @error('phone_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" id="location" name="location" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                        value="{{ old('location') }}" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Name -->
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                    <input type="text" id="business_name" name="business_name" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('business_name') border-red-500 @enderror" 
                        value="{{ old('business_name') }}">
                    @error('business_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Address -->
                <div>
                    <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                    <input type="text" id="business_address" name="business_address" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('business_address') border-red-500 @enderror" 
                        value="{{ old('business_address') }}">
                    @error('business_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Provider Type -->
                <div>
                    <label for="provider_type" class="block text-sm font-medium text-gray-700">Provider Type</label>
                    <select id="provider_type" name="provider_type" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('provider_type') border-red-500 @enderror" 
                        required>
                        <option value="">Select provider type</option>
                        <option value="handyman" {{ old('provider_type') == 'handyman' ? 'selected' : '' }}>Handyman</option>
                        <option value="business_owner" {{ old('provider_type') == 'business_owner' ? 'selected' : '' }}>Business Owner</option>
                    </select>
                    @error('provider_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea id="bio" name="bio" rows="4" 
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection