
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ingilzy')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans leading-relaxed">

    <!-- Navbar -->
    <nav class="bg-white shadow py-4">
        <div class="container mx-auto px-6 flex items-center justify-between">
            <a  class="text-2xl font-bold text-blue-600">Ingilzy</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

</body>
</html>