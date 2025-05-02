<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

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

            // Create order
            $order = Order::create([
                'service_id' => $service->id,
                'buyer_id' => $user->serviceBuyer->user_id,
                'status' => 'pending',
                'total_amount' => $service->price,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'special_instructions' => $request->special_instructions,
            ]);

            // Notify provider about new booking
            Notification::create([
                'user_id' => $service->provider->user_id,
                'title' => 'New Booking Request #' . $order->id,
                'content' => json_encode([
                    'message' => 'New booking #'.$order->id.' for '.$service->title.' (Service ID: '.$service->id.')',
                    'source' => 'landing'
                ]),
                'is_read' => false,
                'notification_type' => 'order_update'
            ]);

            // Notify buyer that booking request was sent
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Booking Request Sent #' . $order->id,
                'content' => json_encode([
                    'message' => 'Your booking request #'.$order->id.' for '.$service->title.' has been sent to the provider (Service ID: '.$service->id.')',
                    'source' => 'landing'
                ]),
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
        // Allow manual updates or provider acceptance
        $isManualUpdate = request()->has('manual_update') && request('manual_update') === 'true';
        $isProvider = $order->service->provider->user_id == Auth::id();

        if (!$isManualUpdate && !$isProvider) {
            return back()->with('error', 'Unauthorized action');
        }

        $order->update(['status' => 'accepted']);

        // Only notify buyer if the provider accepted (not for manual updates by buyer)
        if ($isProvider) {
            Notification::create([
                'user_id' => $order->buyer_id,
                'title' => 'Booking Accepted #' . $order->id,
              'content' => json_encode([
                    'message' => "Your booking #{$order->id} for '{$order->service->title}' was accepted (Service ID: {$order->service_id}",
                    'source' => 'landing'
                ]),
                'is_read' => false,
                'notification_type' => 'order_update'
            ]);
        }

        return back()->with('success', 'Order accepted!');
    }

    public function markInProgress(Order $order)
    {
        // Provider marks order as in progress
        if ($order->service->provider->user_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }

        $order->update(['status' => 'in_progress']);

        // Notify the buyer about progress
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Service Started #' . $order->id,
           'content' => json_encode([
                    'message' =>  "Provider has started working on your order #{$order->id} for '{$order->service->title}' (Service ID: {$order->service_id})",
                    'source' => 'landing'
                ]),
                'is_read' => false,
                'notification_type' => 'order_update'
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

        // Notify the buyer about completion
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Service Completed #' . $order->id,
'content' => json_encode([
                    'message' =>  "Your order #{$order->id} for '{$order->service->title}' has been completed (Service ID: {$order->service_id})",
                    'source' => 'landing'
                ]),
                'is_read' => false,
                'notification_type' => 'order_update'
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
        
        if ($order->status == 'accepted') {
            return back()->with('error', 'Can\'t cancel accepted orders');
        }

        $order->update(['status' => 'cancelled']);

        // Notify the other party about cancellation
        $notificationTo = $isBuyer ? $order->service->provider->user_id : $order->buyer_id;
        $cancelledBy = $isBuyer ? 'buyer' : 'provider';

        Notification::create([
            'user_id' => $notificationTo,
            'title' => 'Order Cancelled #' . $order->id,
            'content' => json_encode([
                'message' => "Your order #{$order->id} for '{$order->service->title}' has been cancelled by the {$cancelledBy} (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);


        // Also notify the cancelling party
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Order Cancelled #' . $order->id,
           'content' => json_encode([
                    'message' =>  "You have cancelled your order #{$order->id} for '{$order->service->title}' (Service ID: {$order->service_id})",
                    'source' => 'landing'
                ]),
                'is_read' => false,
                'notification_type' => 'order_update'
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

    // This method would be called after successful payment
    public function paymentSuccess(Order $order)
    {
        try {
            // Verify order belongs to user
            if ($order->buyer_id != Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
    
            // Verify payment was actually successful
            $payment = Payment::where('order_id', $order->id)
                ->where('payment_status', 'successful')
                ->first();
    
            if (!$payment) {
                Log::warning('Attempt to access success page for unpaid order #'.$order->id);
                return redirect()->route('payment.failed')
                    ->with('error', 'No successful payment found for this order.');
            }
    
            // Only update status if it's still accepted 
            if ($order->status === 'accepted') {
                $order->update(['status' => 'in_progress']);
                
                // Notify buyer about successful payment and order status
                Notification::create([
                    'user_id' => $order->buyer_id,
                    'title' => 'Payment Successful #' . $order->id,
                    'content' => "Your payment for order #{$order->id} ('{$order->service->title}') was successful. Your order is now in progress. (Service ID: {$order->service_id})",
                    'notification_type' => 'payment',
                    'is_read' => false
                ]);
    
                // Notify provider about payment received
                Notification::create([
                    'user_id' => $order->service->provider->user_id,
                    'title' => 'Payment Received #' . $order->id,
                    'content' => "Payment received for order #{$order->id} for '{$order->service->title}' (Service ID: {$order->service_id})",
                    'notification_type' => 'payment',
                    'is_read' => false
                ]);
            }
    
            return view('paymob.success', compact('order'));
    
        } catch (\Exception $e) {
            Log::error('Payment success handling failed: '.$e->getMessage());
            return redirect()->route('payment.failed')
                ->with('error', 'Error confirming your payment. Please contact support.');
        }
    }
    
    public function approveService(Order $order)
    {
        // dd($order);
        $providerName = $order->service->provider->user->name;
        $buyerName = $order->buyer->user->name;
        $order->update(["status" => 'completed']);
        // Notify the provider that the service was approved
        Notification::create([
            'user_id' => $order->service->provider->user->id,
            'title' => 'The buyer '. $buyerName .' has approved your service. order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You service '{$order->service->title}' was approved. (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        // Notify the buyer that the service was approved successfully
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Your approved the service successfully. order #' . $order->id,
            'content' => json_encode([
                'message' =>  "The provider '{$providerName}' was notified about your approval of service '{$order->service->title}'. (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        
        return redirect()->back()->with('success', 'Your service was approved successfully');
    }
    public function rejectService(Order $order)
    {
        // dd($order);
        $providerName = $order->service->provider->user->name;
        $buyerName = $order->buyer->user->name;
        $order->update(["status" => 'disapproved']);
        // Notify the provider that the service was disapproved
        Notification::create([
            'user_id' => $order->service->provider->user->id,
            'title' => 'The buyer '. $buyerName .' has disapproved your service. order #' . $order->id,
            'content' => json_encode([
                'message' =>  "You service '{$order->service->title}' was disapproved. (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        // Notify the buyer that the service was approved successfully
        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => 'Your disapproved the service successfully. order #' . $order->id,
            'content' => json_encode([
                'message' =>  "The provider '{$providerName}' was notified about your disapproval of service '{$order->service->title}'. (Service ID: {$order->service_id})",
                'source' => 'dashboard'
            ]),
            'is_read' => false,
            'notification_type' => 'order_update'
        ]);
        
        return redirect()->back()->with('success', 'Your service was approved successfully');

    }
}
