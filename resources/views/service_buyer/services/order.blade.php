@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100">
    <div class="mb-6">
        <a href="{{ route('buyer.services.show', $service->id) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Service
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Order Service</h2>
                <p class="text-gray-600 mt-1">{{ $service->title }}</p>
            </div>

            <form action="{{ route('buyer.orders.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <div class="mb-6">
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                    <input type="date" name="scheduled_date" id="scheduled_date" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                        min="{{ date('Y-m-d') }}" required>
                    @error('scheduled_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                    <input type="time" name="scheduled_time" id="scheduled_time" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                        required>
                    @error('scheduled_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if($service->service_type == 'on_site')
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Service Location</label>
                    <input type="text" name="location" id="location" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                        placeholder="Enter the address where the service will be performed" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div class="mb-6">
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                    <textarea name="special_instructions" id="special_instructions" rows="4" 
                        class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-green-500 focus:border-green-500"
                        placeholder="Any special requirements or instructions for the service provider"></textarea>
                    @error('special_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 pt-4 mb-4">
                    <h3 class="text-lg font-semibold mb-2">Order Summary</h3>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Service Price</span>
                        <span class="font-medium">{{ $service->price }} EGP</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200">
                        <span class="text-gray-800 font-semibold">Total</span>
                        <span class="text-green-600 font-bold">{{ $service->price }} EGP</span>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150 ease-in-out">
                        Proceed to Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection