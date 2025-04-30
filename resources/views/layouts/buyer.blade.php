<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Service Buyer Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            overflow-x: hidden !important;
        }
        [x-cloak] {
            display: none;
        }
    </style>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body 
    x-data="{ 
        sidebarOpen: false, 
        darkMode: localStorage.getItem('theme') === 'dark', 
        toggleTheme() { 
            this.darkMode = !this.darkMode; 
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); 
            document.documentElement.classList.toggle('dark', this.darkMode); 
        } 
    }" 
    x-init="document.documentElement.classList.toggle('dark', darkMode)" 
    class="bg-gray-100 dark:bg-gray-900 font-sans h-screen w-screen"
>

    <div class="flex h-full w-full relative" x-cloak>
        <!-- Sidebar -->
        <div :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
            class="bg-blue-600 dark:bg-gray-800 text-white dark:text-gray-200 w-64 fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:relative md:flex md:flex-col">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-4 border-b border-blue-700 dark:border-gray-700">
                <a href="http://127.0.0.1:8000/" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                    <i class="fas fa-globe text-white text-2xl mr-2"></i>
                    <span class="text-xl font-bold">IngiZly</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-white dark:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <ul class="flex-1 p-4 space-y-4 overflow-y-auto">
                <li>
                    <a href="http://127.0.0.1:8000/" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-home mr-3"></i> Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.dashboard') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-3"></i> Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.services.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-search mr-3"></i> Browse Services
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.orders.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-shopping-cart mr-3"></i> My Orders
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-user mr-3"></i> Profile
                    </a>
                </li>
                <li>
                    <x-notification-bell 
                        :unreadCount="auth()->user()->notifications()->where('is_read', false)->count()"
                        hoverColor="hover:bg-blue-700 dark:hover:bg-gray-700"
                    />
                </li>
                <!-- Theme Toggle in Sidebar -->
                <li>
                    <button @click="toggleTheme" class="flex items-center px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700 w-full text-left">
                        <i x-show="!darkMode" class="fas fa-moon mr-3"></i>
                        <i x-show="darkMode" class="fas fa-sun mr-3"></i>
                        <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                    </button>
                </li>
            </ul>

            <!-- Logout -->
            <div class="p-4 border-t border-blue-700 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 rounded hover:bg-blue-700 dark:hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
                <div class="flex items-center justify-between p-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-300 focus:outline-none md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-800 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
