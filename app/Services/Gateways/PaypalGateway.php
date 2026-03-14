<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;

class PaypalGateway implements PaymentGatewayInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $testMode;
    protected $baseUrl;

    public function __construct($credentials, $testMode = false)
    {
        $this->clientId = $credentials['client_id'] ?? '';
        $this->clientSecret = $credentials['client_secret'] ?? '';
        $this->testMode = $testMode;
        $this->baseUrl = $testMode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
    }

    public function initiate(array $data)
    {
        // PayPal Standard Form Data
        $formData = [
            'cmd' => '_xclick',
            'business' => $this->clientId, // For standard, usually email or merchant ID
            'item_name' => 'Order ' . $data['udf1'],
            'amount' => $data['amount'],
            'currency_code' => 'USD', // PayPal default, might need config
            'return' => $data['surl'],
            'cancel_return' => $data['curl'],
            'notify_url' => url('api/payment/paypal/notify'), // Webhook
            'custom' => $data['udf1'], // Order Number
        ];

        return [
            'status' => true,
            'type' => 'form_post',
            'url' => $this->baseUrl,
            'data' => $formData
        ];
    }

    public function processCallback(array $data)
    {
        // 1. Verify Payment Status
        $paymentStatus = $data['payment_status'] ?? '';
        if (strcasecmp($paymentStatus, 'Completed') !== 0) {
            return [
                'status' => false,
                'message' => 'Payment status is not Completed: ' . $paymentStatus,
                'payment_status' => 'FAILED' // mapped
            ];
        }

        // 2. Verify Transaction ID
        if (empty($data['txn_id'])) {
            return [
                'status' => false,
                'message' => 'Missing Transaction ID',
                'payment_status' => 'FAILED'
            ];
        }

        return [
            'status' => true,
            'transaction_id' => $data['txn_id'],
            'amount' => $data['mc_gross'] ?? 0,
            'payment_status' => 'COMPLETED',
            'raw_response' => $data
        ];
    }
}
