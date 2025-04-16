@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Complete Your Service Provider Profile</h2>
                    <p class="mb-0">Tell us more about your business</p>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('service_provider.store') }}">
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
                        
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                id="business_name" name="business_name" value="{{ old('business_name') }}">
                            @error('business_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <input type="text" class="form-control @error('business_address') is-invalid @enderror" 
                                id="business_address" name="business_address" value="{{ old('business_address') }}">
                            @error('business_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="provider_type" class="form-label">Provider Type</label>
                            <select class="form-select @error('provider_type') is-invalid @enderror" 
                                id="provider_type" name="provider_type" required>
                                <option value="">Select provider type</option>
                                <option value="handyman" {{ old('provider_type') == 'handyman' ? 'selected' : '' }}>Handyman</option>
                                <option value="bussiness_owner" {{ old('provider_type') == 'bussiness_owner' ? 'selected' : '' }}>Business Owner</option>
                            </select>
                            @error('provider_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                id="bio" name="bio" rows="4">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-3">Complete Registration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection