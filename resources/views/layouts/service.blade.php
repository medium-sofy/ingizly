<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Include your local Tailwind CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Notification Bell (Fixed Position) -->
    <div class="fixed top-4 right-4 z-50">
        <a href="{{ route('notifications.index') }}" class="relative">
            <i class="fas fa-bell text-2xl text-gray-700 hover:text-blue-600 transition"></i>
            @auth
    @php
        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
    @endphp
    @if($unreadCount > 0)
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            {{ $unreadCount }}
        </span>
    @endif
@endauth
        </a>
    </div>

    <!-- Main content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Bootstrap JS for modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>