<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    public function fetchNotifications()
    {
        $notifications = Auth::user()->notifications()
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function ($notification) {
                                  return [
                                      'id' => $notification->id,
                                      'title' => $notification->title,
                                      'content' => $notification->content,
                                      'is_read' => (bool)$notification->is_read,
                                      'created_at' => $notification->created_at->toDateTimeString(),
                                      'notification_type' => $notification->notification_type,
                                      'link' => $this->getNotificationLink($notification)
                                  ];
                              });

        return response()->json(['notifications' => $notifications]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    protected function getNotificationLink($notification)
    {
        // Customize this based on your notification types
        switch ($notification->notification_type) {
            case 'order_update':
                return route('orders.show', ['order' => $notification->id]);
            default:
                return '#';
        }
    }
}