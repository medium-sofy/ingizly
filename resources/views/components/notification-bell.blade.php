@props(['unreadCount' => 0, 'hoverColor' => 'hover:bg-blue-700'])

<a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 rounded {{ $hoverColor }} relative" 
   x-data="{ unreadCount: {{ $unreadCount }} }"

   x-init="
    setInterval(() => {
        fetch('{{ route('notifications.unread-count') }}')
            .then(response => response.json())
            .then(data => {
                unreadCount = data.count;
            });
    }, 30000);
">
    <i class="fas fa-bell mr-3"></i>
    <span>Notifications</span>
    <span x-show="unreadCount > 0" 
          class="absolute left-6 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" 
          x-text="unreadCount"></span>
</a>