<!DOCTYPE html>
<html lang="en"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Service Provider Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            overflow-x: hidden !important;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 dark:text-white font-sans h-screen w-screen">

    <div class="flex h-full w-full relative" x-cloak>

        <!-- Mobile Sidebar + Dark Mode Toggle -->
        <div class="md:hidden fixed top-4 left-4 z-50 flex gap-2">
            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="text-white bg-blue-500 p-2 rounded shadow-lg">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Dark Mode Toggle Button -->
            <button @click="darkMode = !darkMode" class="text-white bg-gray-700 p-2 rounded shadow-lg">
                <template x-if="!darkMode">
                    <i class="fas fa-moon"></i>
                </template>
                <template x-if="darkMode">
                    <i class="fas fa-sun"></i>
                </template>
            </button>
        </div>

        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-full w-64 bg-blue-500 dark:bg-gray-800 text-white flex flex-col z-40 transform transition-transform duration-300 md:translate-x-0"
               :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
            <!-- Logo & Close Button -->
            <div class="p-6 border-b border-blue-700 flex justify-between items-center">
                <a href="/">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-cogs mr-3"></i> Ingizly
                    </h2>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <ul class="flex-1 p-4 space-y-4 overflow-y-auto">
                <li>
                    <a href="{{ route('provider.dashboard') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-home mr-3"></i> Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('provider.services.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-briefcase mr-3"></i> Services
                    </a>
                </li>
                <li>
                    <a href="{{ route('provider.bookings.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-calendar-check mr-3"></i> Bookings
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-star mr-3"></i> Reviews
                    </a>
                </li>
                <li>
                    <a href="{{ route('provider.wallet') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-wallet mr-3"></i> Your Wallet
                    </a>
                </li>
                <li>
                    <x-notification-bell
                        :unreadCount="auth()->user()->notifications()->where('is_read', false)->count()"
                        hoverColor="hover:bg-blue-700"
                    />
                </li>
                <li>
                    <a href="{{ route('service_provider.profile.edit') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-user mr-3"></i> Profile
                    </a>
                </li>

                <li class="hidden md:flex items-center px-4 py-3 rounded hover:bg-blue-700 cursor-pointer" @click="darkMode = !darkMode">
    <template x-if="!darkMode">
        <i class="fas fa-moon mr-3"></i>
    </template>
    <template x-if="darkMode">
        <i class="fas fa-sun mr-3"></i>
    </template>
    <span>Toggle Dark Mode</span>
</li>

            </ul>

            <!-- Logout -->
            <div class="p-4 border-t border-blue-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center w-full justify-center px-4 py-3 rounded bg-red-600 hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-0 md:ml-64 transition-all duration-300 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js (for sidebar toggle and dark mode) -->
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')

</body>
</html>
