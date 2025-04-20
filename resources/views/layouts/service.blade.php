<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingizly - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        [x-cloak] { display: none !important; }
        .nav-container {
            background: linear-gradient(135deg, rgb(66, 15, 150) 0%, rgb(56, 85, 218) 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .notification-bell {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .notification-bell:hover {
            transform: translateY(-2px);
        }
        .unread-indicator {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            width: 1.25rem;
            height: 1.25rem;
            background-color: #ef4444;
            border-radius: 9999px;
            border: 2px solid #4c1d95;
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
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #e9d5ff;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #e9d5ff;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!--  Navbar -->
    <nav class="nav-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="bg-white p-2 rounded-lg shadow-md">
                            <i class='bx bxs-cart-alt text-2xl text-purple-700'></i>
                        </div>
                        <span class="text-white text-2xl font-bold">Ingizly</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    @auth
                        @if(auth()->user()->role === 'service_provider')
                            <a href="{{ route('provider.dashboard') }}" class="nav-link text-white font-medium">
                                <i class='bx bx-grid-alt mr-2'></i> Dashboard
                            </a>
                            <a href="{{ route('services.create') }}" class="nav-link text-white font-medium">
                                <i class='bx bx-plus-circle mr-2'></i> New Service
                            </a>
                        @else
                            <a href="{{ route('buyer.dashboard') }}" class="nav-link text-white font-medium">
                                <i class='bx bx-grid-alt mr-2'></i> Dashboard
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- User Controls -->
                <div class="flex items-center space-x-6">
                    <!-- Notifications - Only show if logged in -->
                    @auth
                        <div class="relative" x-data="notificationDropdown()" x-init="init()">
                            <button @click="toggleDropdown()" 
                                    class="notification-bell p-2 text-white hover:text-purple-200 focus:outline-none">
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
                                 class="notification-panel absolute right-0 mt-2 bg-white border border-gray-200 overflow-hidden z-50">
                                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                                    <div class="flex space-x-4">
                                        <button @click="markAllAsRead()" 
                                                class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                            Mark all as read
                                        </button>
                                        <a href="{{ route('notifications.index') }}" 
                                           class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                            See all
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                    <template x-if="!isLoading && notifications.length === 0">
                                        <div class="px-6 py-8 text-center text-gray-500">
                                            <i class='bx bx-bell-off text-4xl mb-4'></i>
                                            <p class="text-gray-600">No new notifications</p>
                                        </div>
                                    </template>
                                    
                                    <template x-for="(notif, index) in notifications" :key="index">
                                        <a :href="getNotificationLink(notif)"
                                           @click="markAsRead(notif)"
                                           class="block px-6 py-4 hover:bg-gray-50 transition"
                                           :class="{ 'bg-purple-50': !notif.is_read }">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mr-4">
                                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                                        <i class='bx text-xl' 
                                                           :class="getNotificationIcon(notif.notification_type)"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium text-gray-900" x-text="notif.title"></p>
                                                    <p class="text-sm text-gray-600 mt-1" x-text="notif.content"></p>
                                                    <p class="text-xs text-gray-400 mt-2" x-text="formatDate(notif.created_at)"></p>
                                                </div>
                                                <div x-show="!notif.is_read" class="ml-4">
                                                    <span class="h-2.5 w-2.5 rounded-full bg-purple-500 block"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Show login/register buttons if not authenticated -->
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-white hover:text-purple-200 font-medium">
                                <i class='bx bx-log-in mr-1'></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-white text-purple-700 hover:bg-purple-100 px-4 py-2 rounded-lg font-medium transition">
                                <i class='bx bx-user-plus mr-1'></i> Register
                            </a>
                        </div>
                    @endauth
                    
                    <!-- User Profile - Only show if logged in -->
                    @auth
                        <div class="relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-white">
                                        <span class="sr-only">Open user menu</span>
                                        @if(Auth::check())
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-white" 
                                                 src="{{ asset(Auth::user()->profile_image ?? 'path/to/default/image.jpg') }}" 
                                                 alt="{{ Auth::user()->name ?? 'User' }}">
                                        @endif
                                    </button>
                                </x-slot>
                                
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')" class="group">
                                        <i class='bx bx-user mr-2 text-gray-500 group-hover:text-purple-600'></i> Profile
                                    </x-dropdown-link>
                                    @if(Auth::check())
                                        @if((Auth::user()->role ?? '') === 'service_provider')
                                            <x-dropdown-link :href="route('provider.dashboard')" class="group">
                                                <i class='bx bx-grid-alt mr-2 text-gray-500 group-hover:text-purple-600'></i> Dashboard
                                            </x-dropdown-link>
                                        @else
                                            <x-dropdown-link :href="route('buyer.dashboard')" class="group">
                                                <i class='bx bx-grid-alt mr-2 text-gray-500 group-hover:text-purple-600'></i> Dashboard
                                            </x-dropdown-link>
                                        @endif
                                    @endif
                                    <div class="border-t border-gray-200"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();" 
                                                class="group">
                                            <i class='bx bx-log-out mr-2 text-gray-500 group-hover:text-purple-600'></i> Sign out
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Alpine JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @stack('scripts')

    <script>
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
                if (notif.notification_type === 'order_update' && notif.service_id) {
                    return `/services/${notif.service_id}`;
                }
                return '/notifications';
            },

            getNotificationIcon(type) {
                switch(type) {
                    case 'order_update': return 'bx-cart';
                    case 'payment': return 'bx-credit-card';
                    case 'message': return 'bx-message-detail';
                    case 'review': return 'bx-star';
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