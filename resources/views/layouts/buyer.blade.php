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
<body class="bg-gray-100 font-sans h-screen w-screen">

    <div x-data="{ sidebarOpen: false }" class="flex h-full w-full relative" x-cloak>
        <!-- Sidebar -->
        <div :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
            class="bg-green-600 text-white w-64 fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 ease-in-out md:translate-x-0 md:relative md:flex md:flex-col">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-4 border-b border-green-700">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Buyer Dashboard</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <ul class="flex-1 p-4 space-y-4 overflow-y-auto">
                <li>
                    <a href="{{ route('buyer.dashboard') }}" class="flex items-center px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-home mr-3"></i> Overview
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.services.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-search mr-3"></i> Browse Services
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.orders.index') }}" class="flex items-center px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-shopping-cart mr-3"></i> My Orders
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-user mr-3"></i> Profile
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
            </ul>

            <!-- Logout -->
            <div class="p-4 border-t border-green-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 rounded hover:bg-green-700">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between p-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center">
                        <span class="text-gray-800 font-medium">{{ Auth::user()->name }}</span>
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