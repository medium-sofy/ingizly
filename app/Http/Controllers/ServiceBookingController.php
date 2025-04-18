<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceBookingController extends Controller
{
    public function acceptOrder(Order $order)
    {
        $order->update(['status' => 'accepted']);
        return back()->with('success', 'Order accepted!');
    }

    public function confirmOrder(Order $order)
    {
        if ($order->buyer_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action');
        }

        if ($order->status !== 'accepted') {
            return back()->with('error', 'This order cannot be confirmed yet.');
        }

        $order->update(['status' => 'confirmed']);
        return back()->with('success', 'Order confirmed successfully!');
    }

    public function bookService(Request $request, Service $service)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        Order::create([
            'service_id' => $service->id,
            'buyer_id' => Auth::user()->serviceBuyer->user_id,
            'status' => 'pending',
            'total_amount' => $service->price,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'special_instructions' => $request->special_instructions,
        ]);

        return redirect()->route('service.details', $service->id)
                        ->with('success', 'Booking request sent successfully!');
    }
}