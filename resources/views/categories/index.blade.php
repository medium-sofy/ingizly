<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - Ingilzy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans leading-relaxed">

    <!-- Navbar -->
    @include('layouts.navigation')

    <!-- All Categories Section -->
    <section class="py-16 bg-gradient-to-b from-gray-100 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-8 text-center">
                Explore Our Categories
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-12 text-lg">
                Discover a wide range of services tailored to your needs.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category->id) }}"
                       class="group relative bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                        <!-- Category Name -->
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                            {{ $category->name }}
                        </h3>

                        <!-- Category Description -->
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            {{ $category->description ?? 'Explore services in this category.' }}
                        </p>

                        <!-- Arrow Icon -->
                        <div class="absolute bottom-4 right-4 text-blue-500 group-hover:text-blue-700 dark:text-blue-400 dark:group-hover:text-blue-200 transition">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Theme Switcher Logic -->
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
</body>
</html>