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
        $orderId = null;
        $serviceId = null;
        
        // Extract violation ID from title or content
        if (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $notification->title, $matches)) {
            $violationId = $matches[1] ?? $matches[2] ?? null;
        } elseif (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $notification->content, $matches)) {
            $violationId = $matches[1] ?? $matches[2] ?? null;
        }
        
        // Extract review ID
        if (preg_match('/review.*?#(\d+)/i', $notification->content, $matches)) {
            $reviewId = $matches[1] ?? null;
        }
        
        // Extract order ID from content (common pattern in service booking notifications)
        if (preg_match('/order.*?#(\d+)|booking.*?#(\d+)/i', $notification->content, $matches)) {
            $orderId = $matches[1] ?? null;
        }
        
        // Extract service ID from content if mentioned
        if (preg_match('/service id: (\d+)/i', $notification->content, $matches)) {
            $serviceId = $matches[1] ?? null;
        }
        
        // If we have an order ID but no service ID, try to get service ID from order
        if ($orderId && !$serviceId) {
            $order = Order::find($orderId);
            $serviceId = $order->service_id ?? null;
        }
        
        // Route based on notification type and user role
        switch ($notification->notification_type) {
            case 'order_update':
            case 'payment':
                if ($serviceId) {
                    if ($user->role === 'service_buyer') {
                        return route('service.details', $serviceId);
                    } elseif ($user->role === 'service_provider') {
                        return route('provider.services.show', $serviceId);
                    }
                } elseif ($orderId) {
                    $order = Order::find($orderId);
                    if ($order && $order->service_id) {
                        if ($user->role === 'service_buyer') {
                            return route('service.details', $order->service_id);
                        } elseif ($user->role === 'service_provider') {
                            return route('provider.services.show', $order->service_id);
                        }
                    }
                }
                break;
                
            case 'review':
                if ($user->role === 'admin' && $reviewId) {
                    return route('admin.reviews.show', $reviewId);
                } elseif ($user->role === 'admin' && !$reviewId) {
                    return route('admin.reviews.index');
                } elseif ($user->role === 'service_provider') {
                    return route('provider.services.index');
                }
                break;
                
            case 'system':
                if ($user->role === 'admin' && $violationId) {
                    return route('admin.reports.show', $violationId);
                } elseif ($user->role === 'service_buyer') {
                    if ($violationId) {
                        $violation = Violation::find($violationId);
                        return $violation ? route('service.details', $violation->service_id) : route('notifications.index');
                    }
                }
                break;
        }
        
        // Default return for any other cases
        return route('notifications.index');
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