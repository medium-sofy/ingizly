<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingizly - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        [x-cloak] { display: none !important; }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .unread-indicator {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            width: 1.25rem;
            height: 1.25rem;
            background-color: #ef4444;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        .notification-panel {
            width: 28rem;
            max-height: 32rem;
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200" data-user-role="{{ Auth::user()->role ?? '' }}">

    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-800 shadow-sm animate-fadeIn">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Ingizly</a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6 text-base font-medium">
                <a href="{{ route('services.all') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Explore Services</a>
                <a href="{{ url('/#about') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">About Us</a>
                <a href="{{ url('/#contact') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
                @auth
                    <!-- Dashboard Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg shadow-md hover:bg-blue-700 transition">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                @endauth
            </nav>

            <!-- Right side (User Controls) -->
            <div class="flex items-center gap-4">
                <!-- Notifications - Only show if logged in -->
                @auth
                           <x-notifications />

                    <!-- User Profile -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                 alt="Profile Picture" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm text-gray-600 dark:text-gray-300"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-md z-50">
                            <a href="{{ auth()->user()->role === 'service_provider' ? route('service_provider.profile.edit') : route('service_buyer.profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Login and Sign Up -->
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-500 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
                        @endif
                    </div>
                @endauth

                <!-- Theme Toggle -->
                <button @click="toggleTheme"
                        class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <template x-if="darkMode">
                        <i class="fas fa-sun text-yellow-400 text-lg"></i>
                    </template>
                    <template x-if="!darkMode">
                        <i class="fas fa-moon text-gray-600 dark:text-gray-300 text-lg"></i>
                    </template>
                </button>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Alpine JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')

    <script>
    function themeSwitcher() {
        return {
            darkMode: localStorage.getItem('theme') === 'dark',
            init() {
                // Apply the correct theme on load
                this.darkMode = localStorage.getItem('theme') === 'dark';
                document.documentElement.classList.toggle('dark', this.darkMode);
            },
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', this.darkMode);
            }
        }
    }

    
    </script>
</body>
</html>