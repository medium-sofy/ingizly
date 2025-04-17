@extends('layouts.buyer')

@section('content')
<div class="p-4 sm:p-6 bg-gray-100">
    <div class="mb-6">
        <a href="{{ route('buyer.services.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Services
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="md:flex">
            {{-- Service Image --}}
            <div class="md:w-1/3 h-64 md:h-auto">
                @if($service->images->isNotEmpty())
                    <img src="{{ Storage::url($service->images->first()->image_url) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-5xl"></i>
                    </div>
                @endif
            </div>

            {{-- Service Details --}}
            <div class="md:w-2/3 p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $service->title }}</h1>
                        <div class="flex items-center mb-4">
                            <div class="flex items-center mr-4">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>{{ number_format($averageRating, 1) }} ({{ $service->reviews->count() }} reviews)</span>
                            </div>
                            <div class="text-gray-500">
                                <i class="fas fa-eye mr-1"></i> {{ $service->view_count }} views
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $service->price }} EGP</div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-tag mr-2 w-5 text-center"></i>
                        <span>{{ $service->category->name }}</span>
                    </div>
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-user mr-2 w-5 text-center"></i>
                        <span>{{ $service->provider->user->name }}</span>
                    </div>
                    <div class="flex items-center text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 w-5 text-center"></i>
                        <span>{{ $service->location ?? 'Location not specified' }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-cog mr-2 w-5 text-center"></i>
                        <span>
                            @if($service->service_type == 'on_site')
                                On Site Service
                            @elseif($service->service_type == 'remote')
                                Remote Service
                            @else
                                Business Based Service
                            @endif
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-4">
                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                    <p class="text-gray-700">{{ $service->description }}</p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('buyer.services.order', $service->id) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150 ease-in-out">
                        <i class="fas fa-shopping-cart mr-2"></i> Order This Service
                    </a>
                </div>
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="p-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold mb-4">Customer Reviews</h3>
            
            @if($service->reviews->isEmpty())
                <p class="text-gray-500">No reviews yet for this service.</p>
            @else
                <div class="space-y-4">
                    @foreach($service->reviews as $review)
                        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium">{{ $review->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection