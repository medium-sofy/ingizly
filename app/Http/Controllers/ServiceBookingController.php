<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    public function bookService(Request $request, Service $service)
    {
        $request->validate([
            'special_instructions' => 'nullable|string|max:1000',
            'buyer_id' => 'required|integer' // Temporary until auth is implemented
        ]);

        // Create the order
        $order = Order::create([
            'service_id' => $service->id,
            'buyer_id' => $request->buyer_id, // Temporary
            'status' => 'pending',
            'total_amount' => $service->price,
            'special_instructions' => $request->special_instructions
        ]);

        // Notify provider
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

    public function confirmOrder(Request $request, Order $order)
    {
        // Temporary buyer_id check (replace with auth later)
        if ($order->buyer_id != $request->buyer_id) {
            return back()->with('error', 'Unauthorized action');
        }

        // Only allow confirmation if status is 'accepted'
        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order cannot be confirmed yet.');
        }

        $order->update(['status' => 'confirmed']);

        // Notify provider
        Notification::create([
            'user_id' => $order->service->provider->user_id,
            'title' => 'Order Confirmed',
            'content' => 'Order #'.$order->id.' has been confirmed',
            'notification_type' => 'order_update'
        ]);

        return back()->with('success', 'Order confirmed successfully!');
    }
}