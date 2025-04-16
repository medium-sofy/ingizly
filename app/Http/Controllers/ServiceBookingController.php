<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    // Hardcoded buyer ID for temporary development
    protected $tempBuyerId = 16;

    public function bookService(Request $request, Service $service)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        // Create order using Order model directly
        $order = Order::create([
            'service_id' => $service->id,
            'buyer_id' => $this->tempBuyerId,
            'status' => 'pending',
            'total_amount' => $service->price,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'special_instructions' => $request->special_instructions,
        ]);

        // Create notification
        Notification::create([
            'user_id' => $service->provider->user_id,
            'title' => 'New Booking Request',
            'content' => 'You have a new booking request for "'.$service->title.'"',
            'notification_type' => 'order_update',
            'data' => [
                'order_id' => $order->id,
                'service_id' => $service->id
            ]
        ]);

        return redirect()->route('service.details', $service->id)
                         ->with('success', 'Booking request sent successfully!');
    }

    public function confirmOrder(Order $order)
    {
        // Verify the hardcoded buyer owns this order
        if ($order->buyer_id != $this->tempBuyerId) {
            return back()->with('error', 'Unauthorized action');
        }

        // Only allow confirmation if status is 'accepted'
        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order cannot be confirmed yet.');
        }

        $order->update(['status' => 'confirmed']);

        // Create notification
        Notification::create([
            'user_id' => $order->service->provider->user_id,
            'title' => 'Order Confirmed',
            'content' => 'Order #'.$order->id.' has been confirmed',
            'notification_type' => 'order_update'
        ]);

        return back()->with('success', 'Order confirmed successfully!');
    }
}