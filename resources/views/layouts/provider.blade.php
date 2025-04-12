<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Service Provider Dashboard')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white flex flex-col">
            <div class="p-4 border-b border-blue-700">
                <h2 class="text-2xl font-bold">Dashboard</h2>
            </div>
            <ul class="flex-1 p-4 space-y-2">
                <li>
                    <a href="{{ route('provider.dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-700">
                        Overview
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-700">
                        Services
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-700">
                        Bookings
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-700">
                        Reviews
                    </a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 rounded hover:bg-blue-700">
                        Settings
                    </a>
                </li>
            </ul>
            <div class="p-4 border-t border-blue-700">
                <a href="#" class="block px-4 py-2 rounded bg-red-600 text-center hover:bg-red-700">
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>