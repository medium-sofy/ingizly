<!DOCTYPE html>
<html lang="en" x-data="themeSwitcher()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingizly - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        [x-cloak] { display: none !important; }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .unread-indicator {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            width: 1.25rem;
            height: 1.25rem;
            background-color: #ef4444;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        .notification-panel {
            width: 28rem;
            max-height: 32rem;
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200" data-user-role="{{ Auth::user()->role ?? '' }}">

    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-800 shadow-sm animate-fadeIn">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Ingizly</a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6 text-base font-medium">
                <a href="{{ route('services.all') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Explore Services</a>
                <a href="{{ url('/#about') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">About Us</a>
                <a href="{{ url('/#contact') }}" class="text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
                @auth
                    <!-- Dashboard Button -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg shadow-md hover:bg-blue-700 transition">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                @endauth
            </nav>

            <!-- Right side (User Controls) -->
            <div class="flex items-center gap-4">
                <!-- Notifications - Only show if logged in -->
                @auth
                    <div class="relative" x-data="notificationDropdown()" x-init="init()">
                        <button @click="toggleDropdown()"
                                class="p-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition focus:outline-none">
                            <i class='bx bx-bell text-xl'></i>
                            <span x-show="unreadCount > 0" x-text="unreadCount" class="unread-indicator"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="isOpen" x-cloak @click.away="isOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="notification-panel absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
                                <div class="flex space-x-4">
                                    <button @click="markAllAsRead()"
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                        Mark all as read
                                    </button>
                                    <a href="{{ route('notifications.index') }}"
                                       class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                        See all
                                    </a>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                                <template x-if="!isLoading && notifications.length === 0">
                                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class='bx bx-bell-off text-4xl mb-4'></i>
                                        <p>No new notifications</p>
                                    </div>
                                </template>

                                <template x-for="(notif, index) in notifications" :key="index">
                                    <a :href="getNotificationLink(notif)"
                                       @click="markAsRead(notif)"
                                       class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                       :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notif.is_read }">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-4">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                                    <i class='bx text-xl'
                                                       :class="getNotificationIcon(notif.notification_type)"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-gray-100" x-text="notif.title"></p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1" x-text="notif.content"></p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2" x-text="formatDate(notif.created_at)"></p>
                                            </div>
                                            <div x-show="!notif.is_read" class="ml-4">
                                                <span class="h-2.5 w-2.5 rounded-full bg-blue-500 block"></span>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                                 alt="Profile Picture" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm text-gray-600 dark:text-gray-300"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-md z-50">
                            <a href="{{ auth()->user()->role === 'service_provider' ? route('service_provider.profile.edit') : route('service_buyer.profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
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
                    <template x-if="darkMode">
                        <i class="fas fa-sun text-yellow-400 text-lg"></i>
                    </template>
                    <template x-if="!darkMode">
                        <i class="fas fa-moon text-gray-600 dark:text-gray-300 text-lg"></i>
                    </template>
                </button>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Alpine JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')

    <script>
    function themeSwitcher() {
        return {
            darkMode: localStorage.getItem('theme') === 'dark',
            init() {
                // Apply the correct theme on load
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

    function notificationDropdown() {
        return {
            isOpen: false,
            unreadCount: 0,
            notifications: [],
            isLoading: false,

            init() {
                this.fetchUnreadCount();
                setInterval(() => this.fetchUnreadCount(), 30000);
            },

            toggleDropdown() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.fetchNotifications();
                }
            },

            async fetchUnreadCount() {
                try {
                    const response = await fetch('{{ route("notifications.unread-count") }}');
                    const data = await response.json();
                    this.unreadCount = data.count;
                } catch (error) {
                    console.error('Error fetching unread count:', error);
                }
            },

            async fetchNotifications() {
                this.isLoading = true;
                try {
                    const response = await fetch('{{ route("notifications.fetch") }}');
                    const data = await response.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count; // Sync the count
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            async markAsRead(notif) {
                if (notif.is_read) return;

                try {
                    await fetch(`/notifications/${notif.id}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    notif.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            },

            async markAllAsRead() {
                if (this.unreadCount === 0) return;

                try {
                    await fetch('{{ route("notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    // Mark all as read in the UI
                    this.notifications = this.notifications.map(notif => ({
                        ...notif,
                        is_read: true
                    }));
                    this.unreadCount = 0;
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                }
            },

            getNotificationLink(notif) {
                // If the notification has a link property from the API, use it
                if (notif.link && notif.link !== '#') {
                    return notif.link;
                }

                // Use the same routing logic as in index.blade.php
                let link = '#';

                // Extract IDs from notification content
                let violationId = null;
                let reviewId = null;
                let orderId = null;

                // Extract violation ID from title or content
                if ((notif.title && notif.title.match(/violation.*?#(\d+)|report.*?#(\d+)/i))) {
                    const matches = notif.title.match(/violation.*?#(\d+)|report.*?#(\d+)/i);
                    violationId = matches[1] || matches[2];
                } else if ((notif.content && notif.content.match(/violation.*?#(\d+)|report.*?#(\d+)/i))) {
                    const matches = notif.content.match(/violation.*?#(\d+)|report.*?#(\d+)/i);
                    violationId = matches[1] || matches[2];
                }

                // Extract review ID
                if (notif.content && notif.content.match(/review.*?#(\d+)/i)) {
                    const matches = notif.content.match(/review.*?#(\d+)/i);
                    reviewId = matches[1];
                }

                // Extract order ID
                if (notif.content && notif.content.match(/order.*?#(\d+)|booking.*?#(\d+)/i)) {
                    const matches = notif.content.match(/order.*?#(\d+)|booking.*?#(\d+)/i);
                    orderId = matches[1];
                }

                // Extract service ID
                let serviceId = null;
                if (notif.content && notif.content.match(/service id: (\d+)/i)) {
                    const matches = notif.content.match(/service id: (\d+)/i);
                    serviceId = matches[1];
                }

                // Get user role from data attribute or other source
                const userRole = document.body.getAttribute('data-user-role') || '{{ Auth::user()->role ?? "" }}';

                    // Special handling for booking accepted notifications
        if (userRole === 'service_buyer' &&
            notif.title.includes('Booking Accepted') &&
            notif.service_id) {
            return `/services/${notif.service_id}`;
        }


                // For admin role - direct to specific admin routes
                if (userRole === 'admin') {
                    if (notif.notification_type === 'system' && violationId) {
                        return '/admin/reports/' + violationId;
                    }
                    else if (notif.notification_type === 'review' && reviewId) {
                        return '/admin/reviews/' + reviewId;
                    }
                    else if (notif.notification_type === 'review' && !reviewId) {
                        // If we couldn't extract a specific review ID, go to reviews index
                        return '/admin/reviews';
                    }
                }
                // For other roles - existing logic
                else if (notif.notification_type === 'order_update' || notif.notification_type === 'payment') {
                    if (serviceId) {
                        return '/services/' + serviceId;
                    } else if (orderId) {
                        // We may need an API endpoint to get service ID from order ID
                        return '/orders/' + orderId;
                    }
                }
                else if (notif.notification_type === 'system' && userRole === 'service_buyer' && violationId) {
                    // For now, redirect to notifications if we don't have service ID
                    return serviceId ? '/services/' + serviceId : '/notifications';
                }
                else if (notif.notification_type === 'review' && userRole === 'service_provider') {
                    return '/provider/services';
                }

                // Default to notifications page if no specific link is found
                return '/notifications';
            },

            getNotificationIcon(type) {
                switch(type) {
                    case 'order_update': return 'bx-cart';
                    case 'payment': return 'bx-credit-card';
                    case 'message': return 'bx-message-detail';
                    case 'review': return 'bx-star';
                    case 'system': return 'bx-flag';
                    default: return 'bx-bell';
                }
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    }
    </script>
</body>
</html>