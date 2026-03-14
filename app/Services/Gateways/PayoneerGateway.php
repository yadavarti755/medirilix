<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;

class PayoneerGateway implements PaymentGatewayInterface
{
    protected $credentials;

    public function __construct($credentials, $testMode = false)
    {
        $this->credentials = $credentials;
        // Payoneer setup logic
    }

    public function initiate(array $data)
    {
        // Payoneer Checkout Simulation
        // In reality, you would create a "payment_session" via API and get a redirect URL.

        // Simulating API call...
        $token = 'PAYONEER_TOKEN_' . time();
        $redirectUrl = url('api/payoneer/mock-checkout?token=' . $token . '&order=' . $data['udf1']);

        // Since we don't have real Payoneer API keys, we'll return a direct form-like redirect or just success link for now
        // For the sake of this system working without real creds, I will redirect to a generic success handler if in test mode
        if ($this->credentials['test_mode'] ?? false) {
            // Construct a URL that mimics a successful return from Payoneer
            $baseReturnUrl = $data['surl'];
            $successUrl = $baseReturnUrl . '?txnid=' . $data['txnid'] . '&status=approved&udf1=' . $data['udf1'];

            return [
                'status' => true,
                'type' => 'redirect',
                'url' => $successUrl,
                'data' => []
            ];
        }

        return [
            'status' => false,
            'message' => 'Payoneer Live credentials not configured.',
        ];
    }

    public function processCallback(array $data)
    {
        // Verify "approved" status
        $status = $data['status'] ?? '';

        if ($status !== 'approved') {
            return [
                'status' => false,
                'message' => 'Payoneer payment not approved.',
                'payment_status' => 'FAILED'
            ];
        }

        return [
            'status' => true,
            'transaction_id' => $data['txnid'] ?? 'PAYONEER_' . time(),
            'amount' => 0, // Payoneer might not return amount in simple redirect, or we look it up
            'payment_status' => 'COMPLETED',
            'raw_response' => $data
        ];
    }
}
