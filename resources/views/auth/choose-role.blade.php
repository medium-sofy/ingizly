@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2 class="fw-bold mb-0">Choose Your Role</h2>
                        <p class="mb-0">Select how you want to use our platform</p>
                    </div>
                    <div class="card-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-primary hover-shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-4">
                                            <i class="bi bi-person-badge fs-1 text-primary"></i>
                                        </div>
                                        <h3 class="card-title">Service Provider</h3>
                                        <p class="card-text text-muted mb-4">Find your dream job and connect with top employers in your industry.</p>
                                        <form action="{{ route('select.role') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="role" value="service_provider">
                                            <button type="submit" class="btn btn-primary w-100 py-3">Continue as Service Provider</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success hover-shadow">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-4">
                                            <i class="bi bi-building fs-1 text-success"></i>
                                        </div>
                                        <h3 class="card-title">Service Buyer</h3>
                                        <p class="card-text text-muted mb-4">Post jobs and find the perfect candidates for your company's needs.</p>
                                        <form action="{{ route('select.role') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="role" value="service_buyer">
                                            <button type="submit" class="btn btn-success w-100 py-3">Continue as Service Buyer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
    </style>
@endsection
