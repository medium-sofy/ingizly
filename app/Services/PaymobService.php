<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymobService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->baseUrl = 'https://accept.paymobsolutions.com/api';
    }

    public function authenticate()
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey,
        ]);

        return $response->json('token');
    }

    public function createOrder($orderData)
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post("{$this->baseUrl}/ecommerce/orders", $orderData);

        return $response->json();
    }

    public function generatePaymentKey($paymentData)
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post("{$this->baseUrl}/acceptance/payment_keys", $paymentData);

        return $response->json('token');
    }
}