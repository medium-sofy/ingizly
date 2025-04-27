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
            ->paginate(5);

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
                $link = $this->getNotificationLink($notification);
                
                $displayContent = $notification->content;
                try {
                    $decodedContent = json_decode($notification->content, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedContent)) {
                        $displayContent = $decodedContent['message'] ?? $notification->content;
                    }
                } catch (\Exception $e) {
                    // Keep original content if parsing fails
                }
                
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $displayContent,
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
        $violationId = null;
        $reviewId = null;
        $orderId = null;
        $serviceId = null;
        $source = null;
    
        // Parse notification content
        try {
            $decodedContent = json_decode($notification->content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedContent)) {
                $source = $decodedContent['source'] ?? null;
                $contentForRegex = $decodedContent['message'] ?? $notification->content;
            } else {
                $contentForRegex = $notification->content;
            }
        } catch (\Exception $e) {
            $contentForRegex = $notification->content;
        }
    
        // Extract IDs from content
        if (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $notification->title, $matches)) {
            $violationId = $matches[1] ?? $matches[2] ?? null;
        } elseif (preg_match('/violation.*?#(\d+)|report.*?#(\d+)/i', $contentForRegex, $matches)) {
            $violationId = $matches[1] ?? $matches[2] ?? null;
        }
    
        if (preg_match('/review.*?#(\d+)/i', $contentForRegex, $matches)) {
            $reviewId = $matches[1] ?? null;
        }
    
        if (preg_match('/order.*?#(\d+)|booking.*?#(\d+)/i', $contentForRegex, $matches)) {
            $orderId = $matches[1] ?? $matches[2] ?? null;
        } elseif (preg_match('/order.*?#(\d+)|booking.*?#(\d+)/i', $notification->title, $matches)) {
            $orderId = $matches[1] ?? $matches[2] ?? null;
        }
    
        if (preg_match('/service id: (\d+)/i', $contentForRegex, $matches)) {
            $serviceId = $matches[1] ?? null;
        } elseif (preg_match('/\(service id: (\d+)\)/i', $contentForRegex, $matches)) {
            $serviceId = $matches[1] ?? null;
        }
    
        // Get service ID from order if available
        if ($orderId && !$serviceId) {
            $order = Order::find($orderId);
            if ($order) {
                $serviceId = $order->service_id ?? null;
            }
        }
    
        // =============================================
        // SERVICE PROVIDER NOTIFICATIONS (UPDATED)
        // =============================================
        if ($user->role === 'service_provider') {
            // Handle all booking/order/review notifications - always go to provider services
            if (str_contains(strtolower($notification->title), 'booking') || 
                str_contains(strtolower($notification->title), 'order') ||
                str_contains(strtolower($notification->title), 'review') ||
                str_contains(strtolower($notification->title), 'cancel') ||
                $notification->notification_type === 'order_update' ||
                $notification->notification_type === 'review') {
                
                // If we have a specific service ID, go to that service's edit page
                if ($serviceId) {
                    return route('provider.services.show', $serviceId);
                }
                // Otherwise go to services index
                return route('provider.services.index');
            }
    
            // Service approval/rejection
            if (str_contains($notification->title, 'Service Approved') || 
                str_contains($notification->title, 'Service Rejected')) {
                return route('provider.services.index');
            }
    
            // Default for any other provider notifications
            return route('provider.services.index');
        }
    
        // =============================================
        // ADMIN NOTIFICATIONS (ORIGINAL LOGIC)
        // =============================================
        if ($user->role === 'admin') {
            // New service pending approval
            if (str_contains($notification->title, 'New Service Pending Approval')) {
                return route('admin.dashboard');
            }
            
            // Violation reports
            if ($violationId && $notification->notification_type === 'system') {
                return route('admin.reports.show', $violationId);
            }
            
            // Reviews
            if ($reviewId && $notification->notification_type === 'review') {
                return route('admin.reviews.show', $reviewId);
            } elseif ($notification->notification_type === 'review') {
                return route('admin.reviews.index');
            }
        }
    
        // =============================================
        // BUYER NOTIFICATIONS (UPDATED)
        // =============================================
        if ($user->role === 'service_buyer') {
            // Order cancellations - check source
            if (str_contains(strtolower($notification->title), 'cancel')) {
                if ($source === 'dashboard') {
                    return route('buyer.orders.index');
                }
                
                if ($serviceId) {
                    return route('service.details', $serviceId);
                }
                
                return route('buyer.orders.index');
            }
            
            // Booking requests
            if (str_contains($notification->title, 'Booking Request')) {
                if ($source === 'dashboard') {
                    return route('buyer.orders.index');
                }
                
                if ($serviceId) {
                    return route('service.details', $serviceId);
                }
                
                return route('buyer.orders.index');
            }
            
            // Violation notifications
            if ($violationId && $notification->notification_type === 'system') {
                $violation = Violation::find($violationId);
                return $violation ? route('service.details', $violation->service_id) : route('notifications.index');
            }
        }
    
        // Default fallback for any unhandled cases
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