<!-- filepath: \\wsl.localhost\Ubuntu-22.04\home\nour\Final-Project-iti\ingizly\resources\views\components\navbar.blade.php -->
<header class="bg-white dark:bg-gray-800 shadow-sm animate-fadeIn" x-data="{ menuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex justify-between items-center">
        <!-- Logo -->
        <a href="/" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Ingilzy</a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6 text-base font-medium">
            <a href="#services" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Explore Services</a>
            <a href="#about" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">About Us</a>
            <a href="#contact" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-500 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
                    @endif
                @endauth
            @endif
        </nav>

        <!-- Right side (Sign Up + Theme Toggle) -->
        <div class="hidden md:flex items-center gap-2">
            <button @click="toggleTheme"
                    class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <span x-text="darkMode ? '☀ ' : '🌙 '"></span>
            </button>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="md:hidden">
            <button @click="menuOpen = !menuOpen" class="text-gray-700 dark:text-gray-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path x-show="!menuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="menuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <nav x-show="menuOpen" x-transition class="md:hidden px-4 pb-4 space-y-2 bg-white dark:bg-gray-800">
        <a href="#services" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Explore Services</a>
        <a href="#about" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">About Us</a>
        <a href="#contact" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Contact</a>
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block w-full text-center py-2 border border-gray-300 dark:border-gray-600 rounded text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block w-full text-center py-2 bg-blue-600 dark:bg-blue-500 text-white rounded text-sm font-medium hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
                @endif
            @endauth
        @endif
        <button @click="toggleTheme"
                class="w-full text-left py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition">
            <span x-text="darkMode ? '☀ Light Mode' : '🌙 Dark Mode'"></span>
        </button>
    </nav>
</header>