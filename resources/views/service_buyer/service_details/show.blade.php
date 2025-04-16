@extends('layouts.service')

@section('title', $service->title)

@push('styles')
<style>
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating-input input {
        display: none;
    }
    .rating-input label {
        color: #ddd;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .rating-input input:checked ~ label,
    .rating-input input:hover ~ label {
        color: #ffc107;
    }
    .rating-input label:hover,
    .rating-input label:hover ~ label {
        color: #ffc107;
    }
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        overflow-y: auto;
    }
    .modal.show {
        display: block;
    }
    .modal-dialog {
        position: relative;
        margin: 1.75rem auto;
        max-width: 500px;
        width: auto;
    }
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 0.3rem;
        outline: 0;
    }
    .service-image {
        height: 400px;
        object-fit: cover;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="lg:w-2/3">
            <!-- Service Image -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                @if($service->images && count($service->images) > 0)
                    <img src="{{ asset('storage/services/images/' . $service->images[0]->image_url) }}" 
                         alt="Service Image" 
                         class="service-image rounded-t-xl w-full">
                @else
                    <div class="service-image bg-gray-100 flex items-center justify-center rounded-xl">
                        <i class="fas fa-image fa-5x text-gray-300"></i>
                    </div>
                @endif
            </div>

            <!-- Service Details -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">{{ $service->title }}</h1>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-blue-600">${{ number_format($service->price, 2) }}</span>
                            <span class="ml-3 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $service->service_type)) }}
                            </span>
                        </div>
                    </div>

                    @if($service->reviews && $service->reviews->count() > 0)
                    <div class="flex items-center mb-6">
                        <div class="star-rating mr-3">
                            @php $avgRating = $service->reviews->avg('rating'); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-gray-600 mr-4">{{ number_format($avgRating, 1) }} ({{ $service->reviews->count() }} reviews)</span>
                        <span class="text-gray-600"><i class="fas fa-eye mr-1"></i> {{ $service->view_count }} views</span>
                    </div>
                    @endif

                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Description</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Service Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Location</h4>
                                    <p class="text-gray-600 text-sm">{{ $service->location ?? $service->provider->location }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-tag text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Category</h4>
                                    <p class="text-gray-600 text-sm">{{ $service->category->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'service_buyer')
                            <div class="flex flex-wrap gap-4">
                                @php
                                    $currentBuyerId = auth()->id();
                                    $pendingOrder = $service->orders->where('buyer_id', $currentBuyerId)
                                                            ->where('status', 'pending')
                                                            ->first();
                                    $acceptedOrder = $service->orders->where('buyer_id', $currentBuyerId)
                                                             ->where('status', 'accepted')
                                                             ->first();
                                @endphp

                                @if($acceptedOrder)
                                    <form action="{{ route('orders.confirm', $acceptedOrder->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center transition duration-300">
                                            <i class="fas fa-check-circle mr-2"></i> Confirm Order
                                        </button>
                                    </form>
                                @elseif(!$pendingOrder && $service->status === 'active')
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center transition duration-300" onclick="openModal('bookingModal')">
                                        <i class="fas fa-shopping-cart mr-2"></i> Book Now
                                    </button>
                                @elseif($pendingOrder)
                                    <button class="bg-gray-400 text-white px-6 py-3 rounded-lg flex items-center cursor-not-allowed" disabled>
                                        <i class="fas fa-clock mr-2"></i> Pending Approval
                                    </button>
                                @endif

                                <a href="{{ route('service.report.form', $service->id) }}" class="border border-red-500 text-red-500 hover:bg-red-50 px-6 py-3 rounded-lg flex items-center transition duration-300">
                                    <i class="fas fa-flag mr-2"></i> Report
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800">You need to <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">login</a> as a service buyer to book this service.</p>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Reviews Section -->
            @if($service->reviews && $service->reviews->count() > 0)
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-xl font-semibold text-gray-900">Customer Reviews</h3>
                </div>
                <div class="p-6">
                    <!-- Rating Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="text-center border-b md:border-b-0 md:border-r border-gray-200 pb-8 md:pb-0 pr-0 md:pr-8">
                            @php $avgRating = $service->reviews->avg('rating'); @endphp
                            <div class="text-5xl font-bold text-blue-600 mb-3">
                                {{ number_format($avgRating, 1) }}
                            </div>
                            <div class="star-rating mb-4 text-2xl">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avgRating))
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-yellow-400"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-gray-600">{{ $service->reviews->count() }} reviews</div>
                        </div>
                        <div class="space-y-3">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="flex items-center">
                                    <div class="w-10 text-right mr-3">
                                        {{ $i }} <i class="fas fa-star text-yellow-400 ml-1"></i>
                                    </div>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                        @php
                                            $count = $service->reviews->where('rating', $i)->count();
                                            $percentage = $service->reviews->count() > 0 ? ($count / $service->reviews->count()) * 100 : 0;
                                        @endphp
                                        <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="w-12 text-right ml-3 text-sm text-gray-600">
                                        {{ $count }} ({{ round($percentage) }}%)
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach($service->reviews as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 mr-4">
                                        @if($review->buyer->user->profile_image)
                                            <img src="{{ asset($review->buyer->user->profile_image) }}" 
                                                 class="w-full h-full object-cover" 
                                                 alt="{{ $review->buyer->user->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-400 text-white">
                                                {{ substr($review->buyer->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="font-semibold text-gray-900">{{ $review->buyer->user->name }}</h6>
                                        <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="star-rating text-lg">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="pl-16">
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Add Review Form - Only for authenticated buyers who have completed an order -->
            @auth
                @if(auth()->user()->role === 'service_buyer')
                    @php
                        $hasCompletedOrder = $service->orders
                            ->where('buyer_id', auth()->id())
                            ->where('status', 'completed')
                            ->count() > 0;
                        $hasReviewed = $service->reviews
                            ->where('buyer_id', auth()->id())
                            ->count() > 0;
                    @endphp

                    @if($hasCompletedOrder && !$hasReviewed)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="border-b border-gray-200 px-6 py-4">
                                <h3 class="text-xl font-semibold text-gray-900">Write a Review</h3>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('service.review.submit', $service->id) }}" method="POST" id="reviewForm">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $service->orders->where('buyer_id', auth()->id())->where('status', 'completed')->first()->id }}">
                                    <div class="mb-6">
                                        <label class="block text-gray-700 font-medium mb-3">Your Rating</label>
                                        <div class="rating-input">
                                            <input type="radio" id="star5" name="rating" value="5" required/>
                                            <label for="star5" title="5 stars">★</label>
                                            <input type="radio" id="star4" name="rating" value="4"/>
                                            <label for="star4" title="4 stars">★</label>
                                            <input type="radio" id="star3" name="rating" value="3"/>
                                            <label for="star3" title="3 stars">★</label>
                                            <input type="radio" id="star2" name="rating" value="2"/>
                                            <label for="star2" title="2 stars">★</label>
                                            <input type="radio" id="star1" name="rating" value="1"/>
                                            <label for="star1" title="1 star">★</label>
                                        </div>
                                        @error('rating')
                                            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-6">
                                        <label class="block text-gray-700 font-medium mb-3">Your Review</label>
                                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                  name="comment" 
                                                  rows="5" 
                                                  placeholder="Share your experience with this service..." 
                                                  required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-300 w-full">
                                        Submit Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="lg:w-1/3">
            <!-- Provider Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 sticky top-4">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-xl font-semibold text-gray-900">Service Provider</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 mr-5">
                            @if($service->provider->user->profile_image)
                                <img src="{{ asset($service->provider->user->profile_image) }}" 
                                     class="w-full h-full object-cover" 
                                     alt="{{ $service->provider->user->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-400 text-white text-3xl">
                                    {{ substr($service->provider->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-900 text-lg">{{ $service->provider->user->name }}</h5>
                            <div class="flex items-center mt-1">
                                @if($service->provider->is_verified)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full mr-2">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @endif
                                @if($service->provider->avg_rating)
                                <div class="star-rating text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($service->provider->avg_rating))
                                            <i class="fas fa-star text-yellow-400"></i>
                                        @elseif($i - 0.5 <= $service->provider->avg_rating)
                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400"></i>
                                        @endif
                                    @endfor
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Location</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->location }}</p>
                            </div>
                        </div>
                        @if($service->provider->business_name)
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Business</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->business_name }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-phone-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Contact</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->phone_number }}</p>
                            </div>
                        </div>
                    </div>

                    @if($service->provider->bio)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">About</h4>
                        <p class="text-gray-600 text-sm">{{ $service->provider->bio }}</p>
                    </div>
                    @endif

                    @auth
                        <button type="button" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg flex items-center justify-center contact-provider-btn transition duration-300"
                                onclick="openModal('contactProviderModal')"
                                data-phone="{{ $service->provider->phone_number }}">
                            <i class="fas fa-phone-alt mr-2"></i> Contact Provider
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg flex items-center justify-center transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login to Contact
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Provider Modal -->
    <div class="modal" id="contactProviderModal" tabindex="-1" aria-labelledby="contactProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header flex items-center justify-between p-4 border-b">
                    <h5 class="text-xl font-medium text-gray-900">Contact Service Provider</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('contactProviderModal')">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-4 text-gray-700">You can contact the service provider directly using the following methods:</p>
                    <p class="mb-6">Phone: <span id="providerPhoneDisplay" class="font-semibold">{{ $service->provider->phone_number }}</span></p>
                    <div class="flex gap-4 mt-4">
                        <a href="tel:{{ $service->provider->phone_number }}" id="callBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-center transition duration-300">
                            <i class="fas fa-phone mr-2"></i> Call Now
                        </a>
                        <a href="sms:{{ $service->provider->phone_number }}" id="smsBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-center transition duration-300">
                            <i class="fas fa-sms mr-2"></i> Send SMS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    @auth
        @if(auth()->user()->role === 'service_buyer')
            <div class="modal" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header flex items-center justify-between p-4 border-b">
                            <h5 class="text-xl font-medium text-gray-900">Service Booking Summary</h5>
                            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('bookingModal')">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <form id="bookingForm" action="{{ route('service.book', $service->id) }}" method="POST">
                            @csrf
                            
                            <div class="modal-body p-4">
                                <!-- Service Summary -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                    <div class="flex items-start mb-4">
                                        <div class="w-16 h-16 rounded-md overflow-hidden bg-gray-200 mr-4 flex-shrink-0">
                                            @if($service->images && count($service->images) > 0)
                                                <img src="{{ asset('storage/services/images/' . $service->images[0]->image_url) }}" 
                                                     class="w-full h-full object-cover" 
                                                     alt="{{ $service->title }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $service->title }}</h4>
                                            @if($service->reviews && $service->reviews->count() > 0)
                                            @php $avgRating = $service->reviews->avg('rating'); @endphp
                                            <div class="flex items-center mt-1">
                                                <div class="star-rating text-xs mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= floor($avgRating))
                                                            <i class="fas fa-star text-yellow-400"></i>
                                                        @elseif($i - 0.5 <= $avgRating)
                                                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                                                        @else
                                                            <i class="far fa-star text-yellow-400"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-gray-600 text-xs">({{ $service->reviews->count() }})</span>
                                            </div>
                                            @endif
                                            <p class="text-blue-600 font-semibold text-sm mt-1">${{ number_format($service->price, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Category</p>
                                            <p class="text-gray-900">{{ $service->category->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Service Type</p>
                                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Provider</p>
                                            <p class="text-gray-900">{{ $service->provider->user->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Location</p>
                                            <p class="text-gray-900">{{ $service->location ?? $service->provider->location }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Details -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="scheduled_date" class="block text-gray-700 font-medium mb-2">Service Date</label>
                                        <input type="date" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               id="scheduled_date" 
                                               name="scheduled_date" 
                                               min="{{ date('Y-m-d') }}" 
                                               required>
                                    </div>
                                    <div>
                                        <label for="scheduled_time" class="block text-gray-700 font-medium mb-2">Preferred Time</label>
                                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                id="scheduled_time" 
                                                name="scheduled_time">
                                            <option value="08:00:00">Morning (8AM - 12PM)</option>
                                            <option value="12:00:00">Afternoon (12PM - 5PM)</option>
                                            <option value="17:00:00">Evening (5PM - 9PM)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="special_instructions" class="block text-gray-700 font-medium mb-2">Special Instructions</label>
                                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                  id="special_instructions" 
                                                  name="special_instructions" 
                                                  rows="3" 
                                                  placeholder="Any specific requirements or details..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer flex justify-end p-4 border-t">
                                <button type="button" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 mr-3 transition duration-300" onclick="closeModal('bookingModal')">Cancel</button>
                                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                                    <i class="fas fa-paper-plane mr-2"></i> Send Booking Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
@endsection

@push('scripts')
<script>
// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Initialize modals when buttons are clicked
document.addEventListener('DOMContentLoaded', function() {
    // Contact provider modal
    document.querySelectorAll('.contact-provider-btn').forEach(button => {
        button.addEventListener('click', function() {
            const phone = this.getAttribute('data-phone');
            document.getElementById('providerPhoneDisplay').textContent = phone;
            document.getElementById('callBtn').setAttribute('href', 'tel:' + phone);
            document.getElementById('smsBtn').setAttribute('href', 'sms:' + phone);
            openModal('contactProviderModal');
        });
    });

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    });

    // Initialize star rating labels
    document.querySelectorAll('.rating-input label').forEach(label => {
        label.innerHTML = '★';
    });

    // Set minimum date for booking to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('scheduled_date').min = today;
});
</script>
@endpush