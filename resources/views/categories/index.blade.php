<!-- filepath: \\wsl.localhost\Ubuntu-22.04\home\nour\Final-Project-iti\ingizly\resources\views\categories\index.blade.php -->
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
    <section class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-8 text-center">
                All Categories
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($categories as $category)
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            {{ $category->description ?? 'Explore services in this category.' }}
                        </p>
                    </div>
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