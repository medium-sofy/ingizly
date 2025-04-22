@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Violation Details</h1>
            <a href="{{ route('admin.reports.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Service Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Service Title</label>
                            <p class="mt-1">{{ $violation->service->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provider</label>
                            <p class="mt-1">{{ $violation->service->provider->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1">{{ $violation->service->category->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <p class="mt-1">${{ number_format($violation->service->price) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Report Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Report Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reported By</label>
                            <p class="mt-1">{{ $violation->user->name }} ({{ $violation->user->email }})</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reported At</label>
                            <p class="mt-1">{{ $violation->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-block px-2 py-1 text-xs {{
                                $violation->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                ($violation->status == 'investigating' ? 'bg-blue-100 text-blue-800' :
                                ($violation->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
                            }} rounded-full mt-1">
                                {{ ucfirst($violation->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Violation Details -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Violation Details</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="whitespace-pre-wrap">{{ $violation->reason }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Admin Notes</h2>
                <form action="{{ route('admin.reports.update', $violation->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <textarea name="admin_notes" rows="4" 
                                class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Add notes about this violation...">{{ $violation->admin_notes }}</textarea>
                    </div>
                    <div class="flex items-center gap-4">
                        <select name="status" class="border border-gray-300 rounded p-2">
                            <option value="pending" {{ $violation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="investigating" {{ $violation->status == 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ $violation->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="dismissed" {{ $violation->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 