<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymobPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    protected mixed $api_key;
    protected array $integrations_id;

    public function __construct()
    {
        $this->base_url = env("PAYMOB_BASE_URL");
        $this->api_key = env("PAYMOB_API_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $this->integrations_id = [5034939, 5043624];
    }

    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', [
            'api_key' => $this->api_key
        ]);

        return $response->getData(true)['data']['token'];
    }

    public function sendPayment(array|Request $data): array
    {
        $this->header['Authorization'] = 'Bearer ' . $this->generateToken();

        // Prepare full payload
        $payload = [
            'amount_cents' => (int) $data['amount_cents'],
            'currency' => $data['currency'],
            'shipping_data' => [
                'first_name' => $data['first_name'],
                'last_name' => empty($data['last_name']) ? $data['first_name'] : $data['last_name'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
            ],
            'api_source' => 'INVOICE',
            'integrations' => $this->integrations_id,
        ];

        $response = $this->buildRequest('POST', '/api/ecommerce/orders', $payload);

        if ($response->getData(true)['success']) {
            return [
                'success' => true,
                'url' => $response->getData(true)['data']['url']
            ];
        }

        return [
            'success' => false,
            'url' => route('payment.failed')
        ];
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Storage::put('paymob_response.json', json_encode($response));

        return isset($response['success']) && $response['success'] === 'true';
    }
    public function refund(string $transactionId, int $amountCents): array
    {
        $token = $this->generateToken();

        $response = $this->buildRequest('POST', '/api/acceptance/void_refund/refund', [
            'auth_token' => $token,
            'transaction_id' => $transactionId,
            'amount_cents' => $amountCents,
        ]);

        if ($response->getData(true)['success'] === true) {
            return ['success' => true, 'data' => $response->getData(true)];
        }

        return ['success' => false, 'message' => $response->getContent()];
    }

}
