<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Auth::user()->notifications()
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->where('is_read', false)->count();
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
                                      'data' => $notification->data
                                  ];
                              });

        return response()->json(['notifications' => $notifications]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
