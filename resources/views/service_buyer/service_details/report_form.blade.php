@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 font-weight-bold mb-0">Report Service: {{ $service->title }}</h3>
                        <a href="{{ route('service.details', $service->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('service.report.submit', $service->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="reason_type" class="font-weight-bold">Reason for Reporting</label>
                            <select class="form-control" id="reason_type" name="reason_type" required>
                                <option value="">Select a reason</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="False Information">False Information</option>
                                <option value="Spam or Scam">Spam or Scam</option>
                                <option value="Not as Described">Not as Described</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('reason_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="reason" class="font-weight-bold">Detailed Explanation</label>
                            <textarea class="form-control" id="reason" name="reason" rows="5" 
                                      placeholder="Please provide detailed information about your report" required></textarea>
                            <small class="form-text text-muted">
                                Your report will be reviewed by our team. Please provide as much detail as possible.
                            </small>
                            @error('reason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    I confirm that this report is accurate and submitted in good faith
                                </label>
                                @error('agree_terms')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('service.details', $service->id) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-flag mr-2"></i> Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection