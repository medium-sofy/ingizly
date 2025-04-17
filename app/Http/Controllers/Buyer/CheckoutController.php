<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page for an order.
     */
    public function show(Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->buyer_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('service', 'service.provider.user');
        return view('service_buyer.checkout.show', compact('order'));
    }

    /**
     * Process the payment and complete the order.
     */
    public function process(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if ($order->buyer_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Here you would integrate with a payment gateway
        // For now, we'll just mark the order as paid

        $order->update([
            'status' => 'paid',
            // Add payment details as needed
        ]);

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }
}