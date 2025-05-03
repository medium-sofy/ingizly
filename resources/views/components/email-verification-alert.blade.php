@if (Auth::check() && !Auth::user()->is_email_verified)
    <div
        x-data="{ show: true }"
        x-show="show"
        class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded"
        role="alert"
    >
        <div class="flex items-center justify-between">
            <div>
                <strong class="font-bold">Email not verified!</strong>
                <a href="{{ route('verify.otp.form') }}" class="text-blue-600 underline ml-2">Proceed To Verification
                </a>
            </div>
            <button @click="show = false" class="text-yellow-700 hover:text-yellow-900 font-bold text-lg">&times;</button>
        </div>
    </div>
@endif

