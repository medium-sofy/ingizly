<?php

namespace App\Http\Controllers;

use App\Services\PaymobService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    public function createOrder(Request $request)
    {
        $orderData = [
            'merchant_order_id' => $request->input('order_id'),
            'amount_cents' => $request->input('amount') * 100, // Convert to cents
            'currency' => 'EGP',
            'items' => $request->input('items', []),
        ];

        $order = $this->paymobService->createOrder($orderData);

        return response()->json($order);
    }

    public function generatePaymentKey(Request $request)
    {
        $paymentData = [
            'amount_cents' => $request->input('amount') * 100,
            'currency' => 'EGP',
            'order_id' => $request->input('order_id'),
            'billing_data' => $request->input('billing_data'),
            'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        ];

        $paymentKey = $this->paymobService->generatePaymentKey($paymentData);

        return response()->json(['payment_key' => $paymentKey]);
    }
}
