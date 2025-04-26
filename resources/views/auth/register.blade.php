<x-guest-layout>
    <div class="flex flex-col justify-center items-center bg-gray-50">
        <!-- App Name -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-blue-600">Ingizly</h1>
            <p class="text-gray-600 mt-2">Create your account to get started</p>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Profile Image -->
            <div class="mb-4">
                <!-- Upload profile pic -->
                <div class="mb-4">
                    <x-forms.label name="profile_picture" label="Profile Picture"/>
                    <div class="relative w-full">
                        <label for="profile_picture" class="w-full flex items-center justify-center text-white bg-blue-500 hover:bg-blue-400 border border-gray-500 rounded-md py-3 px-4 cursor-pointer">
                            <span class="profile-picture-name">Choose Profile Pic</span>
                        </label>
                        <input id="profile_picture" name="profile_picture" type="file" class="absolute left-0 top-0 opacity-0" onchange="updateFileName('profile_picture', 'profile-picture-name')"/>
                    </div>        
                </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg hover:bg-blue-700 transition shadow-md">
                    {{ __('Register') }}
                </button>
            </div>

            <!-- Already Registered -->
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600 transition">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>
    <script>
    function updateFileName(inputName, fileName) {
        const input = document.getElementById(inputName);
        const fileNameDisplay = document.querySelector('.'+fileName);

        if (input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;
        } else {
            fileNameDisplay.textContent = 'Choose File';
        }
    }
    </script>
</x-guest-layout>
