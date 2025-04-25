<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find trusted local service providers on Ingilzy. Browse, book, and review all in one place.">
<link rel="icon" href="/favicon.ico" type="image/x-icon">

    <title>Ingizly - Find Trusted Services</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out both;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans leading-relaxed">

    <!-- Navbar -->
<header class="bg-white dark:bg-gray-800 shadow-md animate-fadeIn" x-data="{ menuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="/" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Ingizly</a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6 text-base font-medium">
            <a href="#services" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Explore Services</a>
            <a href="#about" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">About Us</a>
            <a href="#contact" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
            @auth
                <!-- Dashboard Button -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg shadow-md hover:bg-blue-700 transition">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            @endauth
        </nav>

        <!-- Right Side (User Info + Theme Toggle) -->
        <div class="flex items-center gap-4">
            @auth
                <!-- User Info -->
                <div class="hidden md:flex items-center gap-2">
                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                         alt="Profile Picture" 
                         class="w-10 h-10 rounded-full object-cover">
                    <span class="text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                </div>
            @else
                <!-- Login and Sign Up -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 dark:bg-blue-500 rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
                    @endif
                </div>
            @endauth

            <!-- Theme Toggle -->
            <button @click="toggleTheme"
                    class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <span x-text="darkMode ? '☀ ' : '🌙 '"></span>
            </button>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="md:hidden">
            <button @click="menuOpen = !menuOpen" class="text-gray-700 dark:text-gray-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path x-show="!menuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="menuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <nav x-show="menuOpen" x-transition class="md:hidden px-4 pb-4 space-y-2 bg-white dark:bg-gray-800">
        <a href="#services" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Explore Services</a>
        <a href="#about" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">About Us</a>
        <a href="#contact" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Contact</a>
        @auth
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>

            <!-- User Info -->
            <div class="flex items-center gap-2 py-2">
                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                     alt="Profile Picture" 
                     class="w-8 h-8 rounded-full object-cover">
                <span class="text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
            </div>
        @else
            <!-- Login and Sign Up -->
            <a href="{{ route('login') }}" class="block w-full text-center py-2 border border-gray-300 dark:border-gray-600 rounded text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Login</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block w-full text-center py-2 bg-blue-600 dark:bg-blue-500 text-white rounded text-sm font-medium hover:bg-blue-700 dark:hover:bg-blue-600 transition">Sign Up</a>
            @endif
        @endauth
        <button @click="toggleTheme"
                class="w-full text-left py-2 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition">
            <span x-text="darkMode ? '☀ Light Mode' : '🌙 Dark Mode'"></span>
        </button>
    </nav>
</header>


    <!-- Hero Section -->
    <section class="relative bg-cover bg-center py-20 sm:py-24 md:py-32 animate-fadeIn dark:text-gray-100"
             style="background-image: url('{{ asset('images/hero-image1.png') }}');">
        <div class="absolute inset-0 bg-white bg-opacity-70 dark:bg-gray-900 dark:bg-opacity-30 backdrop-blur-md"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center px-4 sm:px-6">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">
                Find Trusted Services in Seconds
            </h1>
            <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 mb-10">
                Thousands of small businesses use <span class="text-blue-600 dark:text-blue-400 font-semibold">Ingizly</span> to turn ideas into action.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 mb-12">
                <a href="{{ route('services.all') }}"
                   class="px-6 py-3 sm:px-8 sm:py-4 text-lg font-semibold bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                    Explore Services
                </a>
                <a href="{{ route('register') }}"
                   class="px-6 py-3 sm:px-8 sm:py-4 text-lg font-semibold text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                    Join as Provider
                </a>
            </div>

            <!-- Search Card -->
            <form method="GET"
                  class="bg-white dark:bg-gray-800 bg-opacity-90 backdrop-blur-lg p-6 sm:p-10 rounded-xl shadow-2xl w-full max-w-4xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <!-- Category -->
                    <div x-data="{ open: false, selected: '', selectedId: '' }" class="relative">
                        <label class="block text-sm font-semibold mb-2">Category</label>
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-left text-gray-800 dark:text-gray-200">
                            <span x-text="selected || 'Select Category'"></span>
                        </button>
                        <input type="hidden" name="category" :value="selectedId">

                        <div x-show="open" @click.outside="open = false"
                             class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <ul class="text-sm text-gray-800 dark:text-gray-200">
                                <li @click="selected = 'All Categories'; selectedId = ''; open = false"
                                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">All Categories</li>
                                @foreach ($allCategories as $category)
                                    <li @click="selected = '{{ $category->name }}'; selectedId = '{{ $category->id }}'; open = false"
                                        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">{{ $category->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Service -->
                    <div>
                        <label for="service" class="block text-sm font-semibold mb-2">Service</label>
                        <input type="text" id="service" name="service" placeholder="e.g. Barber, Tutor"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-semibold mb-2">Location</label>
                        <input type="text" id="location" name="location" placeholder="City"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit"
                            class="w-full px-6 py-4 text-lg font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-lg">
                        🔍 Search
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- How It Works -->
   <!-- How It Works -->
<section class="relative py-20 bg-gradient-to-b from-white via-gray-50 to-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 overflow-hidden">
  <div class="container mx-auto px-6 text-center">
    <h2 class="text-4xl md:text-5xl font-extrabold text-gray-700 dark:text-white mb-6 animate-fadeIn">
      How It Works
    </h2>
    <p class="text-lg text-gray-700 dark:text-gray-300 mb-16 max-w-2xl mx-auto animate-fadeIn delay-100">
      A simple 3-step journey to connect with trusted professionals or share your own skills effortlessly.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 animate-fadeIn delay-200">
      <!-- Step 1 -->
      <div class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-8 rounded-3xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300">
        <div class="text-blue-600 dark:text-blue-400 text-4xl mb-4">
          <i class="fas fa-search"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Browse</h3>
        <p class="text-gray-600 dark:text-gray-300">
          Explore thousands of services tailored to your needs by category, keywords, or location.
        </p>
      </div>

      <!-- Step 2 -->
      <div class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-8 rounded-3xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300">
        <div class="text-blue-600 dark:text-blue-400 text-4xl mb-4">
          <i class="fas fa-calendar-check"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Book</h3>
        <p class="text-gray-600 dark:text-gray-300">
          Choose your provider, check availability, and book your appointment in just a few clicks.
        </p>
      </div>

      <!-- Step 3 -->
      <div class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-8 rounded-3xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300">
        <div class="text-blue-600 dark:text-blue-400 text-4xl mb-4">
          <i class="fas fa-star"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Review</h3>
        <p class="text-gray-600 dark:text-gray-300">
          Share your experience and help build a trusted community by rating your service.
        </p>
      </div>
    </div>
  </div>
</section>

    

    <!-- Popular Categories -->
<section class="py-20 bg-gradient-to-b from-gray-100 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4">

        <!-- Section Title -->
        <div class="relative text-center mb-14">
            <div class="inline-block px-8 py-4 bg-white/60 dark:bg-gray-800/60 backdrop-blur-lg rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3 justify-center">
                    <i class="fas fa-rocket text-blue-600 dark:text-blue-400 text-4xl"></i>
                    Explore Popular Categories
                </h2>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach ($popularCategories as $category)
                <a href="{{ route('categories.show', $category->id) }}"
                   class="group relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-lg p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl hover:scale-[1.03] hover:shadow-2xl transition-transform duration-300 ease-out transform hover:-translate-y-1">
                    
                    <!-- Category Icon and Tag -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-3xl text-blue-600 dark:text-blue-400">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <span class="bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300 text-xs font-bold px-3 py-1 rounded-full">
                            Popular
                        </span>
                    </div>

                    <!-- Category Name -->
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                        {{ $category->name }}
                    </h3>

                    <!-- Category Description -->
                    <p class="text-gray-700 dark:text-gray-300 text-sm">
                        {{ $category->description ?? 'Explore services in this category.' }}
                    </p>

                    <!-- Arrow Icon -->
                    <div class="absolute bottom-4 right-4 text-blue-500 group-hover:text-blue-700 dark:text-blue-400 dark:group-hover:text-blue-200 transition">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Show All Button -->
        <div class="mt-14 text-center">
            <a href="{{ route('categories.index') }}"
               class="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold rounded-full shadow-md hover:from-blue-700 hover:to-blue-600 transition">
                🌐 Show All Categories
            </a>
        </div>

    </div>
</section>

<section class="bg-white dark:bg-gray-900 py-16" id="about">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center">
        <!-- Image -->
        <div class="md:w-1/2 mb-10 md:mb-0">
                            <img src="{{ asset('images/about.png') }}" alt="About Ingilzy" class="rounded-lg">
                        </div>

        <!-- Text Content -->
        <div class="md:w-1/2 md:pl-12 text-gray-800 dark:text-gray-200">
            <h2 class="text-4xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-400">
                About Ingizly
            </h2>
            <p class="mb-6 text-lg leading-relaxed">
                Ingilzy helps you find trusted service providers near you—fast, reliable, and rated by real users. We aim to bridge the gap between customers and local professionals through a simple, modern, and trustworthy platform.
            </p>
            <ul class="space-y-4">
                <li class="flex items-start">
                    <span class="text-green-500 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 text-lg">Verified and reviewed service providers</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 text-lg">Simple and seamless booking process</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 text-lg">Empowering local professionals</span>
                </li>
            </ul>
            <div class="mt-8">
                <a href="{{ route('services.all') }}" class="inline-block px-6 py-3 text-lg font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 transition">
                    Explore Services
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Payment Section -->
<section class="bg-gradient-to-br from-gray-100 to-white dark:from-gray-800 dark:to-gray-900 py-16 px-4 sm:px-6 lg:px-8">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-10">
        <h2 class="text-4xl font-bold mb-4 text-blue-600 dark:text-blue-400">Flexible & Secure Payments</h2>

            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                We support trusted payment gateways so your transactions stay safe and smooth. Whether you're booking a service or getting paid, we’ve got you covered.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-10">
            <div class="flex flex-col items-center text-center max-w-xs">
                <div class="text-blue-600 dark:text-blue-400 text-5xl mb-4">
                    <i class="fas fa-lock"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Secure Transactions</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    We use industry-standard encryption to keep your payments safe.
                </p>
            </div>

            <div class="flex flex-col items-center text-center max-w-xs">
                <div class="text-blue-600 dark:text-blue-400 text-5xl mb-4">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Multiple Methods</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Pay with cards, wallets, or bank transfers—whatever suits you best.
                </p>
            </div>

            <div class="flex flex-col items-center text-center max-w-xs">
                <div class="text-blue-600 dark:text-blue-400 text-5xl mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Fast & Reliable</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Confirmed instantly, with clear tracking and zero hidden fees.
                </p>
            </div>
        </div>
    </div>
</section>



    <!-- Contact Section -->
<section id="contact" class="py-16 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Title -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-blue-600 dark:text-blue-400 animate-fadeIn">Get in Touch</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 animate-fadeIn delay-100">
                Have questions or need assistance? Reach out to us, and we’ll get back to you promptly.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Contact Form -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg animate-fadeIn delay-200">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Send Us a Message</h3>
                <form class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name"
                               class="mt-1 w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com"
                               class="mt-1 w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" name="message" rows="4" placeholder="How can we help you?"
                                  class="mt-1 w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full px-6 py-3 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Image -->
            <div class="animate-fadeIn delay-300">
                <img src="{{ asset('images/Contact_us-amico-removebg-preview.png') }}" alt="Contact Us Illustration" class="w-full rounded-xl ">
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-20 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-950">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">
                Frequently Asked Questions
            </h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                Everything you need to know about using Ingizly — from bookings to becoming a provider.
            </p>
        </div>

        <!-- Accordion -->
        <div class="space-y-6" x-data="{ selected: null }">
            @foreach([
                ['question' => 'How does Ingizly work?', 'answer' => 'Ingizly connects customers with trusted service providers. Simply browse services, book an appointment, and leave a review after your experience.'],
                ['question' => 'Is it free to use Ingizly?', 'answer' => 'Yes, it’s free to browse and book services on Ingizly. Service providers may charge for their services.'],
                ['question' => 'How do I become a service provider?', 'answer' => 'To become a service provider, sign up, complete your profile, and start offering services to customers.'],
                ['question' => 'What payment methods are supported?', 'answer' => 'We support secure payments via credit cards, digital wallets, and bank transfers.']
            ] as $index => $faq)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow transition-shadow duration-300 hover:shadow-md">
                    <button 
                        @click="selected === {{ $index }} ? selected = null : selected = {{ $index }}"
                        class="w-full flex justify-between items-center p-5 text-left"
                    >
                        <span class="text-lg font-semibold text-blue-700 dark:text-blue-300">{{ $faq['question'] }}</span>
                        <svg :class="selected === {{ $index }} ? 'rotate-180' : ''" class="w-5 h-5 text-blue-600 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="selected === {{ $index }}" x-collapse class="px-5 pb-5 text-gray-600 dark:text-gray-300 text-base leading-relaxed">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


    <!-- Theme Switcher Logic -->
    <script>
        function themeSwitcher() {
            return {
                darkMode: false,
                init() {
                    this.darkMode = localStorage.getItem('theme') === 'dark';
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                }
            }
        }
    </script>

</body>
</html>
