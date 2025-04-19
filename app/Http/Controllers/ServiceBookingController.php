<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceBookingController extends Controller
{
    public function bookService(Request $request, Service $service)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string',
            'special_instructions' => 'nullable|string|max:1000',
        ]);
    
        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            if (!$user->serviceBuyer) {
                return back()->with('error', 'You need to complete your buyer profile first.');
            }

            // Create order using the service buyer's user_id
            $order = Order::create([
                'service_id' => $service->id,
                'buyer_id' => $user->serviceBuyer->user_id,
                'status' => 'pending',
                'total_amount' => $service->price,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'special_instructions' => $request->special_instructions,
            ]);
    
            // Notify only the provider about new booking
            Notification::create([
                'user_id' => $service->provider->user_id,
                'title' => 'New Booking Request',
                'content' => 'New booking #'.$order->id.' for '.$service->title,
                'is_read' => false,
                'notification_type' => 'order_update'
            ]);
    
            DB::commit();
            
            return redirect()->route('service.details', $service->id)
                ->with('success', 'Booking request sent successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed: '.$e->getMessage());
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    public function acceptOrder(Order $order)
    {
        // Update the order status
        $order->update(['status' => 'accepted']);
    
        // Create notification for buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Booking Accepted',
            'content' => "Your booking #{$order->id} was accepted",
            'notification_type' => 'order_update',
            'is_read' => false
        ]);
    
        return back()->with('success', 'Order accepted!');
    }
    public function markInProgress(Order $order)
    {
        // Provider marks order as in progress
        if ($order->service->provider->user_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }

        $order->update(['status' => 'in_progress']);

        // Notify only the buyer about progress
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Service Started',
            'content' => "Provider has started working on your order #{$order->id}",
            'notification_type' => 'order_update',
            'is_read' => false
        ]);

        return back()->with('success', 'Order marked as in progress!');
    }

    public function completeOrder(Order $order)
    {
        // Provider marks order as completed
        if ($order->service->provider->user_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }

        $order->update(['status' => 'completed']);

        // Notify only the buyer about completion
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Service Completed',
            'content' => "Your order #{$order->id} has been completed",
            'notification_type' => 'order_update',
            'is_read' => false
        ]);

        return back()->with('success', 'Order marked as completed!');
    }

    public function cancelOrder(Order $order)
    {
        $user = Auth::user();
        $isBuyer = $user->serviceBuyer && $order->buyer_id == $user->serviceBuyer->user_id;
        $isProvider = $user->serviceProvider && $order->service->provider->user_id == $user->id;

        if (!$isBuyer && !$isProvider) {
            return back()->with('error', 'Unauthorized action');
        }

        $order->update(['status' => 'cancelled']);

        // Notify the other party about cancellation
        $notificationTo = $isBuyer ? $order->service->provider->user_id : $order->buyer_id;
        Notification::create([
            'user_id' => $notificationTo,
            'title' => 'Order Cancelled',
            'content' => "Order #{$order->id} has been cancelled",
            'notification_type' => 'order_update',
            'is_read' => false
        ]);

        return redirect()->route('service.details', $order->service_id)
                       ->with('success', 'Booking cancelled successfully. You can book this service again.');
    }

    public function showPayment(Order $order)
    {
        // Verify order belongs to user
        if ($order->buyer_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Verify order is accepted
        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order is not ready for payment.');
        }

        // Load required relationships
        $order->load('service', 'service.provider.user');
        
        // Prepare payment data
        $data = [
            "amount_cents" => $order->total_amount * 100,
            "currency" => "EGP",
            "shipping_data" => [
                "first_name" => Auth::user()->name,
                "last_name" => "",
                "phone_number" => Auth::user()->serviceBuyer->phone_number ?? '01010101010',
                "email" => Auth::user()->email,
            ],
            "items" => [
                "name" => $order->id,
            ]
        ];

        return view('paymob.payment', compact('order', 'data'));
    }
}