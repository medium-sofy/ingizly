<script src="https://unpkg.com/alpinejs" defer></script>

<header class="bg-white dark:bg-gray-800 shadow-sm animate-fadeIn" x-data="{ darkMode: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex justify-between items-center">
        <!-- Logo -->
        <a href="/" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Ingizly</a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6 text-base font-medium">
            <a href="#services" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Explore Services</a>
            <a href="#about" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">About Us</a>
            <a href="#contact" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
        </nav>

        <!-- Right side (User Info + Theme Toggle) -->
        <div class="flex items-center gap-4">
            @auth
                <!-- User Profile (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-2">
                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                         alt="Profile Picture"
                         class="w-10 h-10 rounded-full object-cover">
                    <span class="text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                </div>
            @else
                <!-- Login and Sign Up (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-500 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
                    @endif
                </div>
            @endauth

            <!-- Theme Toggle (Uses Global ThemeSwitcher) -->
            <button @click="toggleTheme"
                    class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <span x-text="darkMode ? '☀ Light Mode' : '🌙 Dark Mode'"></span>
            </button>
        </div>
    </div>
</header>
