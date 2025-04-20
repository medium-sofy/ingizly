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
    public function index(Request $request)
    {
        // Start building the query
        $query = Payment::query();

        // Apply Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('order_id', 'like', '%' . $search . '%') // Assuming order_id can be searched as string
                    ->orWhere('payment_gateway', 'like', '%' . $search . '%');
            });
        }

        // Apply Status Filter
        if ($status = $request->input('payment_status')) {
            // Check if status is not 'All Statuses' or empty (handled by the default empty value)
            if ($status !== '') {
                $query->where('payment_status', $status);
            }
        }

        // Apply Gateway Filter (Optional, if you uncommented it in the blade)
        // if ($gateway = $request->input('gateway')) {
        //     if ($gateway !== '') {
        //         $query->where('payment_gateway', $gateway);
        //     }
        // }


        // Apply Date Range Filter (based on created_at)
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Order by creation date, latest first
        $query->orderBy('created_at', 'asc');

        // Paginate the results
        $payments = $query->paginate(10); // You can adjust the number per page

        // Return the view, passing the payments data
        return view('admin.payments.index', [ // Make sure the view path is correct
            'payments' => $payments,
            // Optionally pass request parameters back to pre-fill the form
            'request' => $request,
        ]);
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

        // If sending payment fails immediately, mark the payment as failed
        if ($payment) {
            $payment->update(['payment_status' => 'failed']);
            Cookie::forget('pending_payment_id'); // Clear cookie if payment failed immediately
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
