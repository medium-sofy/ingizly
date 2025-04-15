@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row">

    <div class="col-lg-8">

    <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-0">

                
<img src="{{ $service->primaryImageUrl }}"
     class="img-fluid w-100 h-100 object-fit-cover"
     alt="{{ $service->title }}"
     id="mainServiceImage">

<!-- Thumbnail Gallery -->
<!-- @if($service->images->count() > 1)
<div class="thumbnails-container">
    @foreach($service->images->where('is_primary', false) as $image)
    <div class="thumbnail-item">
        <img src="{{ $image->image_url }}"
             onclick="changeMainImage(this.src)"
             alt="{{ $service->title }}">
    </div>
    @endforeach
</div>
@endif -->
                </div>
            </div>

            <!-- Service Details Section -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h1 class="h2 font-weight-bold text-dark mb-3">{{ $service->title }}</h1>
                    
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="star-rating mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-muted">{{ number_format($averageRating, 1) }} ({{ $totalReviews }} reviews)</span>
                        </div>
                        <div>
                            <span class="h3 font-weight-bold text-primary">${{ number_format($service->price, 2) }}</span>
                            <span class="badge bg-info ms-2">{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</span>
                        </div>
                    </div>


                    <div class="mb-4">
                        <h4 class="h5 font-weight-bold text-dark mb-3">Description</h4>
                        <p class="text-muted">{{ $service->description }}</p>
                    </div>


                    <div class="mb-4">
                        <h4 class="h5 font-weight-bold text-dark mb-3">Service Highlights</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Professional and reliable service
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Quality guaranteed 
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                On-time delivery
                            </li>
                        </ul>
                    </div>


                    <div class="d-flex mt-4">
                    @php
    // Temporary buyer ID (remove when auth is implemented)
    $currentBuyerId = 1; 
    
    $pendingOrder = $service->orders()
        ->where('buyer_id', $currentBuyerId)
        ->where('status', 'pending')
        ->first();
        
    $acceptedOrder = $service->orders()
        ->where('buyer_id', $currentBuyerId)
        ->where('status', 'accepted')
        ->first();
@endphp

@if($acceptedOrder)
    <form action="{{ route('orders.confirm', $acceptedOrder->id) }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="buyer_id" value="{{ $currentBuyerId }}">
        <button type="submit" class="btn btn-primary me-3 px-4">
            <i class="fas fa-check-circle me-2"></i> Confirm Order
        </button>
    </form>
@elseif(!$pendingOrder)
    <button class="btn btn-success me-3 px-4" data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="fas fa-shopping-cart me-2"></i> Book Now
    </button>
@else
    <button class="btn btn-secondary me-3 px-4" disabled>
        <i class="fas fa-clock me-2"></i> Pending Approval
    </button>
@endif
                        <a href="{{ route('service.report.form', $service->id) }}" class="btn btn-outline-danger">
                            <i class="fas fa-flag me-2"></i> Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="h5 font-weight-bold mb-0 text-dark">Customer Reviews</h3>
                </div>
                <div class="card-body">
                    <!-- Rating Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center border-end">
                            <div class="display-4 font-weight-bold text-primary mb-2">
                                {{ number_format($averageRating, 1) }}
                            </div>
                            <div class="star-rating mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-muted">{{ $totalReviews }} reviews</div>
                        </div>
                        <div class="col-md-8">
                            @for($i = 5; $i >= 1; $i--)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-nowrap me-2" style="width: 30px;">
                                        {{ $i }} <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        @php
                                            $percentage = $totalReviews > 0 
                                                ? ($service->reviews->where('rating', $i)->count() / $totalReviews) * 100 
                                                : 0;
                                        @endphp
                                        <div class="progress-bar bg-warning" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-nowrap ms-2" style="width: 40px;">
                                        {{ round($percentage) }}%
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="reviews-list">
                        @foreach($service->reviews as $review)
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light me-3" style="width: 40px; height: 40px; overflow: hidden;">
                                        @if($review->user->profile_image)
                                            <img src="{{ asset($review->user->profile_image) }}" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 alt="{{ $review->user->name }}">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white">
                                                {{ substr($review->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold">{{ $review->user->name }}</h6>
                                        <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="ps-5">
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        </div>
                        @endforeach

                        @if($service->reviews->isEmpty())
                        <div class="text-center py-4">
                            <i class="far fa-comment-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No reviews yet</h5>
                            <p class="text-muted">Be the first to review this service</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add Review Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="h5 font-weight-bold mb-0 text-dark">Write a Review</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('service.review.submit', $service->id) }}" method="POST" id="reviewForm">
    @csrf
    <input type="hidden" name="order_id" value="15"> <!-- Use your valid order ID -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Your Name</label>
                            <input type="text" class="form-control" name="buyer_name" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Your Rating</label>
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
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Your Review</label>
                            <textarea class="form-control" name="comment" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary px-4">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Provider Card -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="h5 font-weight-bold mb-0 text-dark">Service Provider</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-light me-3" style="width: 60px; height: 60px; overflow: hidden;">
                            @if($service->provider->user->profile_image)
                                <img src="{{ asset($service->provider->user->profile_image) }}" 
                                     class="w-100 h-100 object-fit-cover" 
                                     alt="{{ $service->provider->user->name }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white">
                                    {{ substr($service->provider->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold">{{ $service->provider->user->name }}</h5>
                            @if($service->provider->is_verified)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Verified Provider
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-star text-warning me-2"></i>
                            <span>{{ number_format($service->provider->avg_rating, 1) }} ({{ $service->provider->services->count() }} services)</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <span>{{ $service->provider->location }}</span>
                        </div>
                        @if($service->provider->business_name)
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-building text-info me-2"></i>
                            <span>{{ $service->provider->business_name }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="provider-bio mb-3">
                        <p class="text-muted">{{ $service->provider->bio }}</p>
                    </div>

                    <button type="button" 
        class="btn btn-primary w-100 contact-provider-btn" 
        data-bs-toggle="modal"
        data-bs-target="#contactProviderModal"
        data-phone="{{ $service->provider->phone_number }}">
    <i class="fas fa-phone-alt me-2"></i> Contact Provider
</button>
                </div>
            </div>

<!-- Contact Provider Modal -->
<div class="modal fade" id="contactProviderModal" tabindex="-1" aria-labelledby="contactProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactProviderModalLabel">Contact Service Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Phone: <span id="providerPhoneDisplay" class="fw-bold">{{ $service->provider->phone_number }}</span></p>
                <div class="d-flex gap-2 mt-3">
                    <a href="tel:{{ $service->provider->phone_number }}" id="callBtn" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-phone me-2"></i> Call
                    </a>
                    <a href="sms:{{ $service->provider->phone_number }}" id="smsBtn" class="btn btn-success flex-grow-1">
                        <i class="fas fa-sms me-2"></i> SMS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Confirm Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bookingForm" action="{{ route('service.book', $service->id) }}" method="POST">
                @csrf
                <!-- Temporary buyer_id input (remove when auth is implemented) -->
                <input type="hidden" name="buyer_id" value="1"> <!-- Replace with actual buyer ID -->
           
                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Service Summary</h5>
                            <div class="d-flex mb-3">
                                <img src="{{ $service->primaryImageUrl }}" 
                                     class="rounded me-3" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1">{{ $service->title }}</h6>
                                    <div class="text-muted small">
                                        ${{ number_format($service->price, 2) }} • 
                                        {{ $service->category->name }}
                                    </div>
                                    <div class="star-rating small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $averageRating ? '' : '-empty' }} text-warning"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service Provider</label>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light me-2" style="width: 40px; height: 40px; overflow: hidden;">
                                @if($service->provider->user->profile_image)
                                    <img src="{{ asset($service->provider->user->profile_image) }}" 
                                         class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white">
                                        {{ substr($service->provider->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                {{ $service->provider->user->name }}
                                @if($service->provider->is_verified)
                                    <span class="badge bg-success ms-2">Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                        <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- <button type="submit" class="btn btn-primary" id="submitBookingBtn">
                        Send Booking Request
                    </button> -->
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeMainImage(src) {
        document.getElementById('mainServiceImage').src = src;
    }

    $(document).ready(function() {
        // Contact provider modal
        $('#contactProviderModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); 
            const phone = button.data('phone');
            

            const modal = $(this);
            modal.find('#providerPhoneDisplay').text(phone);
            modal.find('#callBtn').attr('href', 'tel:' + phone);
            modal.find('#smsBtn').attr('href', 'sms:' + phone);
        });

        // Initialize star rating
        $('.rating-input label').each(function() {
            $(this).html('★');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Handle booking modal
    const bookingModal = document.getElementById('bookingModal');
    if (bookingModal) {
        bookingModal.addEventListener('show.bs.modal', function() {
            // Any initialization code if needed
        });
    }

    // Disable booking if service is not active
    const serviceStatus = "{{ $service->status }}";
    if(serviceStatus !== 'active') {
        const bookBtn = document.querySelector('[data-bs-target="#bookingModal"]');
        if (bookBtn) {
            bookBtn.disabled = true;
            bookBtn.classList.add('disabled');
            bookBtn.title = 'This service is not currently available for booking';
        }
    }
});
</script>
@endsection  