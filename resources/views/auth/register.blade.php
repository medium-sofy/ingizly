<x-home.layout>

        <x-home.page-heading>
            Register
        </x-home.page-heading>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-forms.input name='name' label='Name'/>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-forms.input name='email' label='Email' type='email' required />
        </div>

        <!-- Profile Image -->
        <div class="mt-4">
            <!-- Upload profile pic -->
            <div class="mt-4">
                <x-forms.label name="profile_picture" label="Profile Picture"/>
                <div class="relative w-full">
                    <label for="profile_picture" class="w-full flex items-center justify-center text-white bg-blue-500 hover:bg-blue-400 border border-gray-500 rounded-md py-3 px-4 cursor-pointer">
                        <span class="profile-picture-name">Choose Profile Pic</span>
                    </label>
                    <input id="profile_picture" name="profile_picture" type="file" class="absolute left-0 top-0 opacity-0" onchange="updateFileName('profile_picture', 'profile-picture-name')"/>
                </div>        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-forms.input name="password" label="Password" type="password" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-forms.input name="password_confirmation" type="password" label="Confirm Password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

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
</x-home.layout>
