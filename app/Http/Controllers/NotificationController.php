<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Will work once auth is implemented
    }

    public function index()
    {
        // Temporary fallback for development
        $userId = Auth::check() ? Auth::id() : 1; // Fallback to user_id 1 if not authenticated
        
        $notifications = Notification::where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        // Temporary bypass for development
        if (!Auth::check()) {
            $notification->update(['is_read' => true]);
            return back()->with('success', 'Notification marked as read');
        }

        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read');
    }
}