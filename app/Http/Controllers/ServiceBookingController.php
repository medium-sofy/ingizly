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
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to book a service.');
        }

        // Check if user has the correct role
        if (Auth::user()->role !== 'service_buyer') {
            return back()->with('error', 'Only service buyers can book services.');
        }

        $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $order = Order::create([
            'service_id' => $service->id,
            'buyer_id' => Auth::id(),
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

        Notification::create([
            'user_id' => $order->service->provider->user_id,
            'title' => 'Order Confirmed',
            'content' => 'Order #'.$order->id.' has been confirmed',
            'notification_type' => 'order_update'
        ]);

        return back()->with('success', 'Order confirmed successfully!');
    }
}