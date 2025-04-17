<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Notification Dropdown -->
    <div class="fixed top-4 right-4 z-50" x-data="notificationDropdown()" x-init="init()">
        <div class="relative">
            <button @click="toggleDropdown()" class="relative focus:outline-none">
                <i class="fas fa-bell text-2xl text-gray-700 hover:text-blue-600 transition"></i>
                <span x-show="unreadCount > 0"
                      x-text="unreadCount"
                      class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                </span>
            </button>

            <div x-show="isOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg overflow-hidden z-50">
                <div class="py-1">
                    <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <span class="text-sm font-semibold">Notifications</span>
                        <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">Mark all as read</button>
                    </div>

                    <template x-if="isLoading">
                        <div class="px-4 py-3 text-sm text-gray-500">Loading notifications...</div>
                    </template>

                    <template x-if="!isLoading && notifications.length === 0">
                        <div class="px-4 py-3 text-sm text-gray-500">No new notifications</div>
                    </template>

                    <template x-if="!isLoading" x-for="notification in notifications" :key="notification.id">
                        <a :href="getNotificationLink(notification)"
                           @click="markAsRead(notification)"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100"
                           :class="{ 'bg-blue-50': !notification.is_read }">
                            <div class="font-medium" x-text="notification.title"></div>
                            <div class="text-gray-500" x-text="notification.content"></div>
                            <div class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></div>
                        </a>
                    </template>

                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-center text-blue-600 hover:bg-gray-100">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <main class="min-h-screen">
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
                    // Close dropdown when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!this.$el.contains(e.target)) {
                            this.isOpen = false;
                        }
                    });
                },

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen && this.notifications.length === 0) {
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
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async markAsRead(notification) {
                    if (notification.is_read) return;

                    try {
                        await fetch(`/notifications/${notification.id}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                        notification.is_read = true;
                        this.unreadCount--;
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
                        this.notifications.forEach(n => n.is_read = true);
                        this.unreadCount = 0;
                    } catch (error) {
                        console.error('Error marking all notifications as read:', error);
                    }
                },

                getNotificationLink(notification) {
                    if (notification.notification_type === 'order_update') {
                        return `/services/${notification.data.service_id}`;
                    }
                    return '#';
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleString();
                }
            }
        }
    </script>
</body>
</html>
