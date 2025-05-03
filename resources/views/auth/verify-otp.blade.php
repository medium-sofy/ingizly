@extends('layouts.minimal')

@section('title', 'Verify Email OTP')

@section('content')
<div class="max-w-md mx-auto mt-12 bg-white shadow-md rounded-xl p-6">
    <h2 class="text-2xl font-bold text-center mb-4">Email Verification</h2>

    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('verify.otp') }}">
        @csrf
        <div class="mb-4">
            <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP Code</label>
            <input id="otp" name="otp" type="text" maxlength="6" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">
            Verify OTP
        </button>
    </form>

    <form method="POST" action="{{ route('resend.otp') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-indigo-600 hover:underline">
            Didn't receive the code? Resend OTP
        </button>
    </form>
</div>
@endsection
