<!DOCTYPE html>
@php use Illuminate\Support\Str; @endphp

@if (!Str::contains(request()->path(), 'admin'))
    
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif

            </main>
         </div>
    </body>
</html>
    
    @else
    
    
    <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingizly | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Optional: Add smooth transition for sidebar */
        #sidebar {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <aside id="sidebar" class="w-64 bg-indigo-700 text-white fixed inset-y-0 left-0 z-30 transform -translate-x-full md:relative md:translate-x-0 md:flex md:flex-col">
        <div class="p-6">
            <h1 class="text-2xl font-semibold">Ingizly | Admin</h1>
        </div>
        <nav class="mt-6 flex-1">
            {{-- Added check for active route for better UX --}}
            <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-white {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-white {{ request()->routeIs('admin.users.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                <i class="fas fa-users mr-3"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="flex items-center px-6 py-3 text-white {{ request()->routeIs('admin.services.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                <i class="fas fa-cogs mr-3"></i>
                <span>Services</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800"> {{-- Add active check later --}}
                <i class="fas fa-folder mr-3"></i>
                <span>Categories</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800"> {{-- Add active check later --}}
                <i class="fas fa-star mr-3"></i>
                <span>Reviews</span>
            </a>
            <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-indigo-800"> {{-- Add active check later --}}
                <i class="fas fa-file-alt mr-3"></i>
                <span>Reports</span>
            </a>
            {{-- Consider adding Logout link here --}}
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="md:hidden bg-white shadow-md p-4 fixed top-0 left-0 right-0 z-20">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-indigo-700">Ingizly | Admin</h1>
                <button id="sidebarToggle" aria-label="Open sidebar" class="text-indigo-700 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </header>

        <main class="flex-1 p-8 pt-20 md:pt-8">
{{--            {/* Adjusted padding-top */}--}}
            @yield('content')
        </main>
    </div>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-50 z-20 hidden md:hidden"></div>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Function to open sidebar
    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
        // Optional: Prevent body scroll when sidebar is open
        // document.body.style.overflow = 'hidden';
    };

    // Function to close sidebar
    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        // Optional: Restore body scroll
        // document.body.style.overflow = 'auto';
    };

    // Event listener for the toggle button
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent click from immediately closing sidebar via overlay
            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }

    // Event listener for the overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            closeSidebar();
        });
    }

    // Close sidebar if clicking outside of it (more robust)
    // document.addEventListener('click', (event) => {
    //     if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && !sidebar.classList.contains('-translate-x-full')) {
    //         closeSidebar();
    //     }
    // });

    // Close sidebar on window resize if screen becomes medium or larger
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) { // Tailwind's `md` breakpoint
            if (!sidebar.classList.contains('-translate-x-full')) {
                closeSidebar(); // Close if it was manually opened on a smaller screen
            }
        }
    });

</script>
@stack('scripts')
</body>
</html>   
    
@endif
