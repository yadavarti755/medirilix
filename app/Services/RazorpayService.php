<?php

namespace App\Services;

use Razorpay\Api\Api;
use App\Models\PaymentGateway;
use Exception;

class RazorpayService
{
    protected $gateway;
    protected $api;

    public function __construct()
    {
        $this->gateway = PaymentGateway::where('gateway_name', 'Razorpay')->first();

        if ($this->gateway && $this->gateway->is_active) {
            $this->api = new Api($this->gateway->client_id_or_key, $this->gateway->client_secret);
        }
    }

    public function createOrder($amount, $receipt, $currency = 'INR')
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            throw new Exception("Razorpay gateway is not active or configured.");
        }

        // Amount in paise
        $orderData = [
            'receipt'         => $receipt,
            'amount'          => round($amount * 100), // Convert to paise and ensure it's an integer
            'currency'        => $currency,
            'payment_capture' => 1 // Auto capture
        ];

        try {
            $razorpayOrder = $this->api->order->create($orderData);
            return $razorpayOrder;
        } catch (Exception $e) {
            throw new Exception("Razorpay Order Creation Failed: " . $e->getMessage());
        }
    }

    public function verifyPayment($attributes)
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            throw new Exception("Razorpay gateway is not active.");
        }

        try {
            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (Exception $e) {
            throw new Exception("Payment signature verification failed: " . $e->getMessage());
        }
    }

    public function fetchPayment($paymentId)
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            throw new Exception("Razorpay gateway is not active.");
        }

        try {
            return $this->api->payment->fetch($paymentId);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch payment details: " . $e->getMessage());
        }
    }

    public function fetchOrder($orderId)
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            throw new Exception("Razorpay gateway is not active.");
        }

        try {
            return $this->api->order->fetch($orderId);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch order details: " . $e->getMessage());
        }
    }

    public function getKeyId()
    {
        return $this->gateway->client_id_or_key ?? null;
    }
}
