@extends('layouts.minimal')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white shadow-xl rounded-lg overflow-hidden">
        <!-- Title Section -->
        <div class="text-center py-6">
            <h2 class="text-3xl font-bold text-blue-600">Complete Your Profile</h2>
            <p class="text-sm text-gray-600 mt-2">Just a few more details to get started</p>
        </div>

        <!-- Form -->
        <div class="p-8">
            <form method="POST" action="{{ route('service_buyer.store') }}" class="space-y-6">
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