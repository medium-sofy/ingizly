@extends('layouts.service')

@section('title', $service->title)

@push('styles')
<style>
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating-input input { display: none; }
    .rating-input label {
        color: #ddd;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .rating-input input:checked ~ label,
    .rating-input input:hover ~ label,
    .rating-input label:hover,
    .rating-input label:hover ~ label {
        color: #ffc107;
    }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background:rgba(0,0,0,0.5); z-index:1050; overflow-y:auto;}
    .modal.show { display: block; }
    .modal-dialog { margin: 2rem auto; max-width: 480px; }
    .modal-content { background: #fff; border-radius: 1.25rem; box-shadow: 0 8px 32px rgba(0,0,0,0.12);}
    .service-image { height: 340px; object-fit: cover; width: 100%; }
</style>
@endpush

@section('content')
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-7xl mx-auto px-2 sm:px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Service Image -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                @if($service->images && count($service->images) > 0)
                    <img src="{{ asset('storage/' . $service->images[0]->image_url) }}" alt="Service Image" class="service-image w-full">
                @else
                    <div class="service-image bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-image fa-4x text-gray-300"></i>
                    </div>
                @endif
            </div>

            <!-- Service Details -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $service->title }}</h1>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-bold text-blue-700 tracking-wide">${{ number_format($service->price, 2) }}</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-100 shadow-sm">
                            {{ ucfirst(str_replace('_', ' ', $service->service_type)) }}
                        </span>
                    </div>
                </div>

                @if($service->reviews && $service->reviews->count() > 0)
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-1">
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
                    <span class="text-gray-600 text-sm">{{ number_format($avgRating, 1) }} ({{ $service->reviews->count() }} reviews)</span>
                    <span class="text-gray-400 text-xs"><i class="fas fa-eye mr-1"></i>{{ $service->view_count }} views</span>
                </div>
                @endif

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3">
                        <span class="bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-map-marker-alt text-blue-600"></i></span>
                        <div>
                            <h4 class="font-medium text-gray-900">Location</h4>
                            <p class="text-gray-600 text-sm">{{ $service->location ?? $service->provider->location }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-tag text-blue-600"></i></span>
                        <div>
                            <h4 class="font-medium text-gray-900">Category</h4>
                            <p class="text-gray-600 text-sm">{{ $service->category->name }}</p>
                        </div>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->role === 'service_buyer')
                        @php
                            $currentUser = auth()->user();
                            $hasBuyerProfile = $currentUser->serviceBuyer;
                            $userOrders = $hasBuyerProfile ? $service->orders->where('buyer_id', $currentUser->serviceBuyer->user_id) : collect();
                            $currentOrder = $userOrders->where('status', '!=', 'cancelled')->sortByDesc('created_at')->first();
                            $hasCompletedOrder = $userOrders->where('status', 'completed')->isNotEmpty();
                            $hasReported = $currentUser->violations()->where('service_id', $service->id)->exists();
                        @endphp
                        <div class="space-y-3">
                            @if(!$hasBuyerProfile)
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded flex items-center gap-2 text-sm">
                                    <i class="fas fa-exclamation-circle text-yellow-400"></i>
                                    Complete your <a href="{{ route('service_buyer.form') }}" class="font-medium underline">buyer profile</a> to book services.
                                </div>
                            @elseif(!$currentOrder && $service->status === 'active')
                                @if($hasCompletedOrder)
                                    <div class="bg-gray-50 border-l-4 border-gray-400 p-3 rounded flex items-center gap-2 text-sm">
                                        <i class="fas fa-check-double text-gray-400"></i>
                                        Service was previously completed.
                                    </div>
                                @endif
                                <button onclick="openModal('bookingModal')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center justify-center shadow transition">
                                    <i class="fas fa-calendar-plus mr-2"></i> {{ $hasCompletedOrder ? 'Book Again' : 'Book Now' }}
                                </button>
                            @elseif($currentOrder)
                                <div class="@switch($currentOrder->status)
                                        @case('pending') bg-blue-50 border-l-4 border-blue-400 @break
                                        @case('accepted') bg-green-50 border-l-4 border-green-400 @break
                                        @case('in_progress') bg-blue-50 border-l-4 border-blue-400 @break
                                        @case('completed') bg-gray-50 border-l-4 border-gray-400 @break
                                    @endswitch p-3 rounded flex items-center gap-2 text-sm">
                                    <i class="@switch($currentOrder->status)
                                            @case('pending') fas fa-clock text-blue-400 @break
                                            @case('accepted') fas fa-check-circle text-green-400 @break
                                            @case('in_progress') fas fa-tasks text-blue-400 @break
                                            @case('completed') fas fa-check-double text-gray-400 @break
                                        @endswitch"></i>
                                    <span>
                                        @switch($currentOrder->status)
                                            @case('pending') Your booking request is pending approval. @break
                                            @case('accepted') Your booking has been accepted! Please confirm to proceed. @break
                                            @case('in_progress') Your service is in progress. Scheduled for {{ $currentOrder->scheduled_date->format('M j') }} at {{ date('g:i A', strtotime($currentOrder->scheduled_time)) }}. @break
                                            @case('completed') Service completed on {{ $currentOrder->updated_at->format('M j, Y') }}. @break
                                        @endswitch
                                    </span>
                                </div>
                                @switch($currentOrder->status)
                                    @case('pending')
                                        <form action="{{ route('orders.cancel', $currentOrder->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl flex items-center justify-center shadow transition">
                                                <i class="fas fa-times mr-2"></i> Cancel Booking
                                            </button>
                                        </form>
                                        @break
                                    @case('accepted')
                                        <div class="space-y-2 mt-2">
                                            <form action="{{ route('order.payment', $currentOrder->id) }}" method="GET">
                                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl flex items-center justify-center shadow transition">
                                                    <i class="fas fa-check-double mr-2"></i> Confirm & Pay Now
                                                </button>
                                            </form>
                                            <div class="bg-gray-100 p-2 rounded text-center text-xs text-gray-600">
                                                <i class="fas fa-info-circle mr-1"></i> Cancellation not available after acceptance
                                            </div>
                                        </div>
                                        @break
                                    @case('completed')
                                        @if($service->status === 'active')
                                            <button onclick="openModal('bookingModal')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center justify-center shadow transition mt-2">
                                                <i class="fas fa-redo mr-2"></i> Book Again
                                            </button>
                                        @endif
                                        @break
                                @endswitch
                            @endif

                            @if(!$hasReported)
                                <a href="{{ route('service.report.form', $service->id) }}" class="border border-red-500 text-red-500 hover:bg-red-50 px-6 py-3 rounded-xl flex items-center justify-center shadow transition">
                                    <i class="fas fa-flag mr-2"></i> Report Service
                                </a>
                            @else
                                <div class="border border-gray-300 text-gray-500 px-6 py-3 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-flag mr-2"></i> You've already reported this service
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded flex items-center gap-2 text-sm">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        <a href="{{ route('login') }}" class="font-medium underline">Login</a> as a service buyer to book this service.
                    </div>
                @endauth
            </div>

            <!-- Reviews Section -->
            @if($service->reviews && $service->reviews->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Reviews</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="text-center border-b md:border-b-0 md:border-r border-gray-200 pb-8 md:pb-0 pr-0 md:pr-8">
                        @php $avgRating = $service->reviews->avg('rating'); @endphp
                        <div class="text-4xl font-bold text-blue-700 mb-2">{{ number_format($avgRating, 1) }}</div>
                        <div class="flex justify-center mb-2 text-xl">
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
                    <div class="space-y-2">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center gap-2">
                                <div class="w-8 text-right">{{ $i }} <i class="fas fa-star text-yellow-400 ml-1"></i></div>
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                    @php
                                        $count = $service->reviews->where('rating', $i)->count();
                                        $percentage = $service->reviews->count() > 0 ? ($count / $service->reviews->count()) * 100 : 0;
                                    @endphp
                                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="w-10 text-right text-xs text-gray-600">{{ $count }} ({{ round($percentage) }}%)</div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="space-y-6">
                    @foreach($service->reviews as $review)
                    <div class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                    @if($review->buyer->user->profile_image)
                                        <img src="{{ asset($review->buyer->user->profile_image) }}" class="w-full h-full object-cover" alt="{{ $review->buyer->user->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-400 text-white">
                                            {{ substr($review->buyer->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="font-semibold text-gray-900">{{ $review->buyer->user->name }}</h6>
                                    <p class="text-xs text-gray-500">{{ $review->created_at ? $review->created_at->format('M d, Y') : 'Date not available' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-1 text-yellow-400 text-base">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="pl-14">
                            <p class="text-gray-700 text-sm">{{ $review->comment }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Add Review Form -->
            @auth
                @if(auth()->user()->role === 'service_buyer')
                    @php
                        $hasCompletedOrder = $service->orders()
                            ->where('buyer_id', auth()->id())
                            ->where('status', 'completed')
                            ->exists();
                    @endphp
                    @if($hasCompletedOrder)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Write a Review</h3>
                            <form action="{{ route('service.review.submit', $service->id) }}" method="POST" id="reviewForm">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->id }}">
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Your Rating</label>
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
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Your Review</label>
                                    <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" name="comment" rows="4" placeholder="Share your experience..." required></textarea>
                                </div>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl w-full shadow transition">Submit Review</button>
                            </form>
                        </div>
                    @endif
                @endif
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 sticky top-6 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Service Provider</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                            @if($service->provider->user->profile_image)
                                <img src="{{ asset($service->provider->user->profile_image) }}" class="w-full h-full object-cover" alt="{{ $service->provider->user->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-400 text-white text-2xl">
                                    {{ substr($service->provider->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-900">{{ $service->provider->user->name }}</h5>
                            <div class="flex items-center gap-2 mt-1">
                                @if($service->provider->is_verified)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                @endif
                                @if($service->provider->avg_rating)
                                    <div class="flex gap-0.5 text-yellow-400 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($service->provider->avg_rating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $service->provider->avg_rating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-map-marker-alt text-blue-600"></i></span>
                            <div>
                                <h4 class="font-medium text-gray-900">Location</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->location }}</p>
                            </div>
                        </div>
                        @if($service->provider->business_name)
                        <div class="flex items-start gap-3">
                            <span class="bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-building text-blue-600"></i></span>
                            <div>
                                <h4 class="font-medium text-gray-900">Business</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->business_name }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-start gap-3">
                            <span class="bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-phone-alt text-blue-600"></i></span>
                            <div>
                                <h4 class="font-medium text-gray-900">Contact</h4>
                                <p class="text-gray-600 text-sm">{{ $service->provider->phone_number }}</p>
                            </div>
                        </div>
                    </div>
                    @if($service->provider->bio)
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 mb-1">About</h4>
                        <p class="text-gray-600 text-sm">{{ $service->provider->bio }}</p>
                    </div>
                    @endif
                    @auth
                        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center contact-provider-btn shadow transition"
                                onclick="openModal('contactProviderModal')" data-phone="{{ $service->provider->phone_number }}">
                            <i class="fas fa-phone-alt mr-2"></i> Contact Provider
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl flex items-center justify-center shadow transition">
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
                        <a href="tel:{{ $service->provider->phone_number }}" id="callBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-xl text-center shadow transition">
                            <i class="fas fa-phone mr-2"></i> Call Now
                        </a>
                        <a href="sms:{{ $service->provider->phone_number }}" id="smsBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-xl text-center shadow transition">
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
                                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
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
                                <button type="button" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 mr-3 transition" onclick="closeModal('bookingModal')">Cancel</button>
                                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow transition">
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
function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = 'auto';
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.contact-provider-btn').forEach(button => {
        button.addEventListener('click', function() {
            const phone = this.getAttribute('data-phone');
            document.getElementById('providerPhoneDisplay').textContent = phone;
            document.getElementById('callBtn').setAttribute('href', 'tel:' + phone);
            document.getElementById('smsBtn').setAttribute('href', 'sms:' + phone);
            openModal('contactProviderModal');
        });
    });
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    });
    document.querySelectorAll('.rating-input label').forEach(label => {
        label.innerHTML = '★';
    });
    const today = new Date().toISOString().split('T')[0];
    if(document.getElementById('scheduled_date')) document.getElementById('scheduled_date').min = today;
});
</script>
@endpush