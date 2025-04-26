<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services in {{ $category->name }} - Ingilzy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        function themeSwitcher() {
            return {
                darkMode: localStorage.getItem('theme') === 'dark',
                init() {
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
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans leading-relaxed">

    <!-- Navbar -->
    @include('layouts.navigation')

    <!-- Services Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-8 text-center">
                Services in {{ $category->name }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($services as $service)
                    <!-- Wrap the service card in a link -->
                    <a href="{{ route('service.details', $service->id) }}" class="block bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="mb-4">
                            @if ($service->images->isNotEmpty())
                                @if (Str::startsWith($service->images->first()->image_url, ['http://', 'https://']))
                                    <img src="{{ $service->images->first()->image_url }}" alt="{{ $service->title }}" class="w-full h-40 object-cover rounded-lg">
                                @else
                                    <img src="{{ asset('storage/' . $service->images->first()->image_url) }}" alt="{{ $service->title }}" class="w-full h-40 object-cover rounded-lg">
                                @endif
                            @else
                                <div class="w-full h-40 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $service->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            {{ Str::limit($service->description, 100) }}
                        </p>
                        <div class="text-blue-600 dark:text-blue-400 font-bold text-lg">
                            ${{ number_format($service->price, 2) }}
                        </div>
                    </a>
                @empty
                    <p class="text-gray-600 dark:text-gray-400 text-center col-span-full">
                        No services found in this category.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

</body>
</html>