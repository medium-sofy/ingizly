<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function paymentProcess(Request $request)
    {
        $data = $request->only(['amount_cents', 'currency', 'first_name', 'last_name', 'phone_number', 'email','order_id']);

        $order = Order::findOrFail($data['order_id']);

        $payment = Payment::where('order_id', $order->id)
            ->where('payment_status', 'pending')
            ->first();

        if ($payment) {
            // If a pending payment exists, update its details if necessary
            $payment->update([
                'amount' => $order->total_amount, // Ensure amount is correct
                'currency' => $data['currency'], // Ensure currency is correct
            ]);
        } else {
            // If no pending payment exists, create a new one
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_gateway' => 'Paymob',
                'amount' => $order->total_amount,
                'currency' => $data['currency'],
                'payment_status' => 'pending',
                // transaction_id will be added in the callback
            ]);
        }

        Cookie::queue('pending_payment_id', $payment->id, 60); // Cookie will expire in 60 minutes

        $response = $this->paymentGateway->sendPayment($data);

        if ($response['success']) {
            return redirect()->away($response['url']);
        }

        return redirect()->route('payment.failed');
    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->paymentGateway->callBack($request);
        if ($response) {


            $pendingPaymentId = Cookie::get('pending_payment_id'); // Retrieve from cookie
            $payment = Payment::findOrFail($pendingPaymentId);
            $payment->update([
                'payment_status' => 'successful',
                'transaction_id' => $request->get('id'),
            ]);
            Cookie::forget('pending_payment_id');
            return redirect()->route('payment.success');
        } else {
            $pendingPaymentId = Cookie::get('pending_payment_id'); // Retrieve from cookie
            if ($pendingPaymentId) {
                $payment = Payment::findOrFail($pendingPaymentId);
                $payment->update(['payment_status' => 'failed']);

            }
        }
        return redirect()->route('payment.failed');

    }
    public function success()
    {
        return view('paymob.payment-success');
    }

    public function failed()
    {
        return view('paymob.payment-failed');
    }
}
