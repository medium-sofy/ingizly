<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Active Services - Ingilzy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-gray-100 font-sans leading-relaxed">

    <!-- Navbar -->
    @include('layouts.navigation')

    <!-- Filters and Search Section -->
    <section class="py-12 bg-white dark:bg-gray-900 shadow-inner">
        <div class="container mx-auto px-6">
            <form method="GET" action="{{ route('services.all') }}"
                  class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                
                <!-- Search Bar -->
                <div class="w-full md:w-1/2 relative">
                    <input type="text" name="search" id="search"
                           placeholder="🔍 Search services..."
                           value="{{ request('search') }}"
                           class="w-full px-5 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <!-- Category Filter -->
                <div class="w-full md:w-1/3">
                    <select name="category" id="categoryFilter"
                            class="w-full px-5 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full font-semibold shadow-md hover:from-blue-700 hover:to-indigo-700 transition">
                        🎯 Apply
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- All Services Section -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-black text-center mb-12 bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-teal-400 dark:from-blue-300 dark:to-purple-300">
                🚀 All Active Services
            </h2>

            <div id="servicesGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
                @foreach ($services as $service)
                    <a href="{{ route('service.details', $service->id) }}"
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-[1.02] duration-300"
                       data-category="{{ $service->category->id }}"
                       data-title="{{ strtolower($service->title) }}"
                       data-description="{{ strtolower($service->description) }}">

                        <!-- Service Image -->
                        <div class="mb-4 overflow-hidden rounded-xl">
                            @if ($service->images->isNotEmpty())
                                @if (Str::startsWith($service->images->first()->image_url, ['http://', 'https://']))
                                    <img src="{{ $service->images->first()->image_url }}" alt="{{ $service->title }}"
                                         class="w-full h-44 object-cover transform transition group-hover:scale-105">
                                @else
                                    <img src="{{ asset('storage/' . $service->images->first()->image_url) }}" alt="{{ $service->title }}"
                                         class="w-full h-44 object-cover transform transition group-hover:scale-105">
                                @endif
                            @else
                                <div class="w-full h-44 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                            @endif
                        </div>

                        <!-- Service Title -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                            {{ $service->title }}
                        </h3>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ Str::limit($service->description, 100) }}
                        </p>

                        <!-- Category Label -->
                        <span class="inline-block text-xs font-medium px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full">
                            {{ $service->category->name }}
                        </span>

                        <!-- Arrow Icon -->
                        <div class="absolute bottom-4 right-4 text-blue-500 group-hover:text-blue-700 dark:text-blue-400 dark:group-hover:text-blue-200 transition">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 text-center">
                {{ $services->links('pagination::tailwind') }}
            </div>
        </div>
    </section>

    <!-- Theme Switcher Script -->
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

        // Filter Services on client-side
        function filterServices() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const services = document.querySelectorAll('#servicesGrid a');

            services.forEach(service => {
                const title = service.getAttribute('data-title');
                const desc = service.getAttribute('data-description');
                const category = service.getAttribute('data-category');

                const matchesSearch = title.includes(searchInput) || desc.includes(searchInput) || searchInput === '';
                const matchesCategory = category === categoryFilter || categoryFilter === '';

                service.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
            });
        }
    </script>

</body>
</html>
