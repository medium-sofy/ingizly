<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        $count = Auth::user()->notifications()->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    public function fetch()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $serviceId = null;
                if ($notification->notification_type === 'order_update') {
                    $orderId = $this->extractOrderId($notification->content);
                    $serviceId = Order::find($orderId)->service_id ?? null;
                }

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'is_read' => (bool)$notification->is_read,
                    'created_at' => $notification->created_at->toDateTimeString(),
                    'notification_type' => $notification->notification_type,
                    'service_id' => $serviceId
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->notifications()->where('is_read', false)->count()
        ]);
    }

    protected function extractOrderId($content)
    {
        preg_match('/#(\d+)/', $content, $matches);
        return $matches[1] ?? null;
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)  
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        // For AJAX requests (like from the dropdown)
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        // For regular form submissions (on notifications page)
        return redirect()->back()->with('success', 'All notifications have been marked as read');
    }
}