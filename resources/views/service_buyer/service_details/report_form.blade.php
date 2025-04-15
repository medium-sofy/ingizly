@extends('layouts.service')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-800">Report Service: {{ $service->title }}</h3>
                    <a href="{{ route('service.details', $service->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <form action="{{ route('service.report.submit', $service->id) }}" method="POST">
                    @csrf
                    <!-- Reason Type Select -->
                    <div class="mb-6">
                        <label for="reason_type" class="block text-sm font-medium text-gray-700 mb-2">Reason for Reporting</label>
                        <select id="reason_type" name="reason_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select a reason</option>
                            <option value="Inappropriate Content">Inappropriate Content</option>
                            <option value="False Information">False Information</option>
                            <option value="Spam or Scam">Spam or Scam</option>
                            <option value="Not as Described">Not as Described</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('reason_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Detailed Explanation -->
                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Detailed Explanation</label>
                        <textarea id="reason" name="reason" rows="5" required
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md p-3"
                            placeholder="Please provide detailed information about your report"></textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Your report will be reviewed by our team. Please provide as much detail as possible.
                        </p>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms Agreement -->
                    <div class="mb-8">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="agree_terms" name="agree_terms" type="checkbox" required
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="agree_terms" class="font-medium text-gray-700">I confirm that this report is accurate and submitted in good faith</label>
                            </div>
                        </div>
                        @error('agree_terms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('service.details', $service->id) }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-flag mr-2"></i> Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection