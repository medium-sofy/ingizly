<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceBookingController extends Controller
{
    public function bookService(Request $request, Service $service)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $order = Order::create([
            'service_id' => $service->id,
            'buyer_id' => Auth::user()->serviceBuyer->user_id,
            'status' => 'pending',
            'total_amount' => $service->price,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'special_instructions' => $request->special_instructions,
        ]);

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
        // Verify the authenticated buyer owns this order
        if ($order->buyer_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }

        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order cannot be confirmed yet.');
        }

        $order->update(['status' => 'confirmed']);

        $service = $order->service;

        // Create notification for the buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Order Confirmed',
            'content' => 'Your order for "'.$service->title.'" has been confirmed',
            'notification_type' => 'order_update',
            'data' => [
                'order_id' => $order->id,
                'service_id' => $service->id
            ]
        ]);

        return back()->with('success', 'Order confirmed successfully!');
    }
}
