<!DOCTYPE html>
<html lang="en">
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
<body class="bg-gray-100 font-sans h-screen w-screen">

    <div x-data="{ sidebarOpen: false }" class="flex h-full w-full relative" x-cloak>

        <!-- Mobile Sidebar Toggle Button -->
        <div class="md:hidden fixed top-4 left-4 z-50">
            <button @click="sidebarOpen = !sidebarOpen" class="text-white bg-blue-500 p-2 rounded shadow-lg z-50">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-full w-64 bg-blue-500 text-white flex flex-col z-40 transform transition-transform duration-300 md:translate-x-0"
               :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

            <!-- Logo and Title -->
            <div class="p-6 border-b border-blue-700 flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-cogs mr-3"></i> ingizly
                </h2>
                <button @click="sidebarOpen = false" class="md:hidden text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <ul class="flex-1 p-4 space-y-4 overflow-y-auto">
                <li>
                    <a href="{{ route('provider.dashboard') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-home mr-3"></i> Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('services.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-briefcase mr-3"></i> Services
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-calendar-check mr-3"></i> Bookings
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-star mr-3"></i> Reviews
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded hover:bg-blue-700">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
            </ul>

            <!-- Logout Button -->
            <div class="p-4 border-t border-blue-700">
                <a href="#" class="flex items-center justify-center px-4 py-3 rounded bg-red-600 hover:bg-red-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-0 md:ml-64 transition-all duration-300 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js (for sidebar toggle) -->
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')

</body>
</html>