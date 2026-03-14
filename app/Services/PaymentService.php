<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Product; // For stock decrement
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // For error logging
use Exception;

class PaymentService
{
    protected $payPalService;
    protected $razorpayService;

    public function __construct()
    {
        $this->payPalService = new \App\Services\PayPalService();
        $this->razorpayService = new \App\Services\RazorpayService();
    }

    // ... (keep generateUniqueId, generateOrderNumber, generateInvoiceNumber if needed, or remove if unused)

    public function processPayment(Order $order, $gateway)
    {
        $total = $order->total_price;

        if (strtolower($gateway->gateway_name) == 'paypal') {
            try {
                $response = $this->payPalService->createOrder($total, 'USD', route('payment.paypal.success'), route('payment.paypal.cancel'));

                if (isset($response['id']) && $response['status'] == 'CREATED') {
                    session()->put('current_order_id', $order->id);
                    session()->put('paypal_order_id', $response['id']);

                    foreach ($response['links'] as $link) {
                        if ($link['rel'] == 'approve') {
                            return ['status' => true, 'action' => 'redirect', 'url' => $link['href']];
                        }
                    }
                }
                return ['status' => false, 'message' => 'Something went wrong with PayPal.'];
            } catch (Exception $e) {
                return ['status' => false, 'message' => $e->getMessage()];
            }
        } elseif (strtolower($gateway->gateway_name) == 'razorpay') {
            try {
                $siteSettings = \App\Models\SiteSetting::with('currency')->first();
                $currency = $siteSettings->currency->currency ?? 'INR';

                $razorpayOrder = $this->razorpayService->createOrder($total, $order->order_number, $currency);

                if (isset($razorpayOrder['id'])) {
                    session()->put('current_order_id', $order->id);
                    session()->put('razorpay_order_id', $razorpayOrder['id']);

                    // Get User Details for Razorpay Pre-fill
                    $user = Auth::user();
                    $contact = ''; // Fetch from address if available

                    return [
                        'status' => true,
                        'action' => 'view',
                        'view' => 'website.payment.razorpay',
                        'data' => [
                            'key' => $this->razorpayService->getKeyId(),
                            'amount' => $razorpayOrder['amount'],
                            'currency' => $currency,
                            'order_number' => $order->order_number,
                            'order_id' => $razorpayOrder['id'],
                            'callback_url' => route('payment.razorpay.callback'),
                            'cancel_url' => route('payment.razorpay.cancel'),
                            'user_name' => $user->name,
                            'user_email' => $user->email,
                            'user_contact' => $contact
                        ]
                    ];
                }
                return ['status' => false, 'message' => 'Something went wrong with Razorpay.'];
            } catch (Exception $e) {
                return ['status' => false, 'message' => $e->getMessage()];
            }
        }

        return ['status' => false, 'message' => 'Unsupported payment gateway.'];
    }

    public function verifyPayPal($paypalOrderId)
    {
        try {
            $response = $this->payPalService->capturePayment($paypalOrderId);
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                return ['status' => true, 'transaction_id' => $response['id']];
            }
            return ['status' => false, 'message' => 'Payment failed or not completed.'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyRazorpay($attributes, $expectedOrderId = null)
    {
        try {
            // 1. Local Signature Verification
            $this->razorpayService->verifyPayment($attributes);

            // 2. Remote Status Verification
            $payment = $this->razorpayService->fetchPayment($attributes['razorpay_payment_id']);

            if ($payment['status'] != 'captured') {
                return ['status' => false, 'message' => 'Payment status is ' . $payment['status'] . ', not captured.'];
            }

            // 3. Strict Order ID Check (Prevent Cross-Order Replay)
            if ($expectedOrderId && $attributes['razorpay_order_id'] !== $expectedOrderId) {
                return ['status' => false, 'message' => 'Invalid Order ID mismatch.'];
            }

            return ['status' => true, 'transaction_id' => $attributes['razorpay_payment_id']];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getOrderByNumber($orderNumber)
    {
        return Order::where('order_number', $orderNumber)->first();
    }

    public function fetchRazorpayOrder($orderId)
    {
        return $this->razorpayService->fetchOrder($orderId);
    }
}
