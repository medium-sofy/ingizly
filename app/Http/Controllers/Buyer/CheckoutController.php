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
        $data = [
            "amount_cents" => $order->total_amount * 100, // assuming `total` is in EGP
            "currency" => "EGP",
            "shipping_data" => [
                "first_name" => Auth::user()->name,
                "last_name" => "", // or split name if needed
                "phone_number" => Auth::user()->serviceBuyer->phone_number ?? '01010101010', // default if null
                "email" => Auth::user()->email,
            ]


        ];
        return view('paymob.payment', compact('order', 'data'));
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
