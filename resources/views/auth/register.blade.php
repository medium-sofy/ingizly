<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-50">
        <!-- App Name -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-blue-600">Ingizly</h1>
            <p class="text-gray-600 mt-2">Create your account to get started</p>
        </div>

        <!-- Global Error Alert -->
        @if ($errors->any())
            <div class="mb-6 w-full max-w-md bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong class="block mb-2">Whoops! Something went wrong:</strong>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg" x-data="{ imagePreview: null }">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600" />
            </div>

            <!-- Profile Image -->
            <div class="mb-4" x-data="{ fileInput: null }">
                <x-input-label for="profile_image" :value="__('Profile Image')" />
                <input
                    id="profile_image"
                    class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                    type="file"
                    name="profile_image"
                    accept="image/*"
                    x-ref="fileInput"
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            imagePreview = URL.createObjectURL(file);
                        }
                    "
                />
                <x-input-error :messages="$errors->get('profile_image')" class="mt-2 text-red-600" />

                <!-- Preview -->
                <template x-if="imagePreview">
                    <div class="relative mt-4">
                        <img :src="imagePreview" alt="Profile Preview" class="w-32 h-32 object-cover rounded-full mx-auto border shadow-md" />
                        <button type="button"
                            class="absolute top-0 right-0 bg-white border border-red-500 text-red-600 rounded-full p-1 hover:bg-red-600 hover:text-white transition"
                            @click.prevent="
                                imagePreview = null;
                                $refs.fileInput.value = null;
                            "
                        >
                            &times;
                        </button>
                    </div>
                </template>
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
</x-guest-layout>
