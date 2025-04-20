<x-guest-layout>
    <div class="flex flex-col justify-center items-center bg-gray-50 py-12">
        <!-- App Name -->
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-extrabold text-blue-600 tracking-wide">
                Ingizly
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Welcome back! Please log in to your account.</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Email Address -->
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-3">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition shadow-md">
                    {{ __('Log in') }}
                </button>
            </div>

            <!-- Register Link -->
            <div class="mt-3 text-center">
                <p class="text-sm text-gray-600">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                        {{ __('Sign up') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
