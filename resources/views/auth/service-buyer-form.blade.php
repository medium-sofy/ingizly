@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Complete Your Service Buyer Profile</h2>
                    <p class="mb-0">Just a few more details to get started</p>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('service_buyer.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                id="location" name="location" value="{{ old('location') }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success py-3">Complete Registration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection