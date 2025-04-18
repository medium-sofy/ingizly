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
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        .unread-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Notification Dropdown -->
    <div class="fixed top-4 right-4 z-50" x-data="notificationDropdown()" x-init="init()">
    <div class="relative">
        <button @click="toggleDropdown()"
                class="notification-bell focus:outline-none">
            <i class="fas fa-bell text-2xl text-gray-700 hover:text-blue-600 transition"></i>
            <span x-show="unreadCount > 0" x-text="unreadCount" class="unread-count"></span>
        </button>

        <div x-show="isOpen" x-cloak @click.away="isOpen = false"
             class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg overflow-hidden z-50">
            <div class="py-1">
                <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <span class="text-sm font-semibold">Notifications</span>
                    <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">
                        Mark all as read
                    </button>
                </div>

                <template x-if="!isLoading && notifications.length === 0">
                    <div class="px-4 py-3 text-sm text-gray-500">No new notifications</div>
                </template>

                <template x-for="(notif, index) in notifications" :key="index">
                    <a :href="getNotificationLink(notif)"
                       @click="markAsRead(notif)"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100"
                       :class="{ 'bg-blue-50': !notif.is_read }">
                        <div class="font-medium" x-text="notif.title"></div>
                        <div class="text-gray-500" x-text="notif.content"></div>
                        <div class="text-xs text-gray-400 mt-1"
                             x-text="new Date(notif.created_at).toLocaleString()"></div>
                    </a>
                </template>
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
// In your layout/service.blade.php
function notificationDropdown() {
    return {
        isOpen: false,
        unreadCount: 0,
        notifications: [],
        isLoading: false,

        init() {
            this.fetchUnreadCount();
            setInterval(() => this.fetchUnreadCount(), 30000); // Poll every 30 seconds
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
            } catch (error) {
                console.error('Error fetching notifications:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async markAsRead(notification) {
            try {
                await fetch(`/notifications/${notification.id}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                notification.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
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
                console.error('Error marking all as read:', error);
            }
        },

    // Update the getNotificationLink function in your service.blade.php file

function getNotificationLink(notification) {
    if (notification.notification_type === 'order_update') {
        // Extract order ID from content using regex if it exists
        const orderIdMatch = notification.content.match(/#(\d+)/);
        if (orderIdMatch && orderIdMatch[1]) {
            return `/orders/${orderIdMatch[1]}`;
        }
        // Default orders page if no specific ID found
        return '/buyer/orders';
    } else if (notification.notification_type === 'payment') {
        return '/payments';
    } else if (notification.notification_type === 'message') {
        return '/messages';
    } else if (notification.notification_type === 'review') {
        return '/reviews';
    } else {
        return '#';
    }
}
    }
}
</script>
</body>
</html>
