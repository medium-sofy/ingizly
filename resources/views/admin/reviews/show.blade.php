@extends('layouts.sidbar')

@section('content')
    <div class="py-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Review Details</h1>
            <a href="{{ route('admin.reviews.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
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
                            <p class="mt-1">{{ $review->service->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provider</label>
                            <p class="mt-1">{{ $review->service->provider->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="mt-1">{{ $review->service->category->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <p class="mt-1">${{ number_format($review->service->price) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Review Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Review Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reviewer</label>
                            <p class="mt-1">{{ $review->user->name }} ({{ $review->user->email }})</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rating</label>
                            <div class="flex items-center mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-block px-2 py-1 text-xs {{
                                $review->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                ($review->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')
                            }} rounded-full mt-1">
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Review Date</label>
                            <p class="mt-1">{{ $review->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Comment -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Review Comment</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="whitespace-pre-wrap">{{ $review->comment }}</p>
                </div>
            </div>

            <!-- Order Information -->
            @if($review->order)
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Order Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order ID</label>
                        <p class="mt-1">{{ $review->order->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order Date</label>
                        <p class="mt-1">{{ $review->order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order Status</label>
                        <p class="mt-1">{{ ucfirst($review->order->status) }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Admin Actions -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Admin Actions</h2>
                <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4">
                        <select name="status" class="border border-gray-300 rounded p-2">
                            <option value="pending" {{ $review->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $review->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $review->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
