<div class="card mb-4">
    <div class="card-header">
        <h5>Customer Reviews</h5>
    </div>
    <div class="card-body">
        @if($service->reviews->count() > 0)
            @foreach($service->reviews as $review)
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between">
                        <h6>{{ $review->buyer->user->name ?? 'Anonymous' }}</h6>
                        <div class="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star text-warning"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="mt-2">{{ $review->comment }}</p>
                    <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                </div>
            @endforeach
        @else
            <p>No reviews yet.</p>
        @endif
    </div>
</div>