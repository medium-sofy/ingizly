<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Violation;
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
                // Get the appropriate link for this notification
                $link = $this->getNotificationLink($notification);

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'is_read' => (bool)$notification->is_read,
                    'created_at' => $notification->created_at->toDateTimeString(),
                    'notification_type' => $notification->notification_type,
                    'link' => $link
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->notifications()->where('is_read', false)->count()
        ]);
    }
    protected function getNotificationLink($notification)
    {
        $user = Auth::user();

        // Extract IDs from notification content for routing
        $violationId = null;
        $reviewId = null;

        // Extract violation/report ID from title or content
        if (preg_match('/(?:violation|report).*?#(\d+)/i', $notification->title, $matches)) {
            $violationId = $matches[1] ?? null;
        } elseif (preg_match('/(?:violation|report).*?#(\d+)/i', $notification->content, $matches)) {
            $violationId = $matches[1] ?? null;
        }

        // Extract review ID from content
        if (preg_match('/review.*?#(\d+)/i', $notification->content, $matches)) {
            $reviewId = $matches[1] ?? null;
        } elseif (preg_match('/review.*?#(\d+)/i', $notification->title, $matches)) {
            $reviewId = $matches[1] ?? null;
        }

        // Extract order ID from content
        $orderId = null;
        if (preg_match('/order.*?#(\d+)/i', $notification->content, $matches)) {
            $orderId = $matches[1] ?? null;
        }

        // Route based on notification type and user role
        switch ($notification->notification_type) {
            case 'order_update':
                if ($user->role === 'service_buyer' || $user->role === 'service_provider') {
                    $order = Order::find($orderId);
                    return $order ? route('service.details', $order->service_id) : '#';
                }
                break;

            case 'review':
                if ($user->role === 'admin' && $reviewId) {
                    return route('admin.reviews.show', $reviewId);
                } elseif ($user->role === 'service_provider') {
                    return route('provider.services.index');
                }
                break;

            case 'system':
            case 'violation_update':  // Make sure to catch this type too
                if ($user->role === 'admin' && $violationId) {
                    return route('admin.reports.show', $violationId);
                } elseif ($user->role === 'service_buyer') {
                    if ($violationId) {
                        $violation = Violation::find($violationId);
                        return $violation ? route('service.details', $violation->service_id) : '#';
                    } else {
                        $violation = Violation::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->first();
                        return $violation ? route('service.details', $violation->service_id) : '#';
                    }
                }
                break;
        }

        // Default return for any other cases
        return route('notifications.index');
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications have been marked as read');
    }
}
