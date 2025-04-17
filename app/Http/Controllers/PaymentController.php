<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function paymentProcess(Request $request)
    {
        $data = $request->only(['amount_cents', 'currency', 'first_name', 'last_name', 'phone_number', 'email']);

        $response = $this->paymentGateway->sendPayment($data);

        if ($response['success']) {
            return redirect()->away($response['url']);
        }

        return redirect()->route('payment.failed');
    }

    public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = $this->paymentGateway->callBack($request);

        return $response
            ? redirect()->route('payment.success')
            : redirect()->route('payment.failed');
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
