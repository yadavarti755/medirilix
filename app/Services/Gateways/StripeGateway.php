<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class StripeGateway implements PaymentGatewayInterface
{
    protected $secretKey;
    protected $publishableKey;

    public function __construct($credentials, $testMode = false)
    {
        $this->secretKey = $credentials['secret_key'] ?? '';
        $this->publishableKey = $credentials['publishable_key'] ?? '';
    }

    public function initiate(array $data)
    {
        // Using Stripe Checkout Session via HTTP
        $response = Http::withBasicAuth($this->secretKey, '')
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'success_url' => $data['surl'] . '?session_id={CHECKOUT_SESSION_ID}&gateway_code=stripe',
                'cancel_url' => $data['curl'],
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Order ' . $data['udf1'],
                            ],
                            'unit_amount' => (int) ($data['amount'] * 100), // In Cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'client_reference_id' => $data['udf1'], // Order Number
                'customer_email' => $data['email'],
            ]);

        if ($response->successful()) {
            $session = $response->json();
            return [
                'status' => true,
                'type' => 'redirect',
                'url' => $session['url'],
                'data' => []
            ];
        }

        return [
            'status' => false,
            'message' => 'Stripe Error: ' . ($response->json()['error']['message'] ?? 'Unknown error'),
        ];
    }

    public function processCallback(array $data)
    {
        $sessionId = $data['session_id'] ?? null;
        if (!$sessionId) {
            return ['status' => false, 'message' => 'No session ID found'];
        }

        // Verify Session
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);

        if ($response->successful()) {
            $session = $response->json();
            if ($session['payment_status'] == 'paid') {
                return [
                    'status' => true,
                    'transaction_id' => $session['payment_intent'] ?? $sessionId,
                    'amount' => $session['amount_total'] / 100,
                    'payment_status' => 'COMPLETED',
                    'raw_response' => $session
                ];
            }
        }

        return ['status' => false, 'message' => 'Payment verification failed'];
    }
}
