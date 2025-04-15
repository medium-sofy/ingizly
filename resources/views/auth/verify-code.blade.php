@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Enter Verification Code') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.verify') }}">
                        @csrf

                        <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

                        <div class="row mb-3">
                            <label for="code" class="col-md-4 col-form-label text-md-end">
                                {{ __('Verification Code') }}
                            </label>

                            <div class="col-md-6">
                                <input id="code" type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       name="code" required autocomplete="off" autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verify Email') }}
                                </button>

                                <a class="btn btn-link" href="{{ route('verification.send') }}" 
                                   onclick="event.preventDefault(); document.getElementById('resend-form').submit();">
                                    {{ __('Resend Code') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <form id="resend-form" action="{{ route('verification.send') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection