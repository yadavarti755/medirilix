<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Config;
use Exception;

class PayPalService
{
    protected $gateway;

    public function __construct()
    {
        $this->gateway = PaymentGateway::where('gateway_name', 'PAYPAL')->first();
    }

    protected function getClient()
    {
        if (!$this->gateway || !$this->gateway->is_active) {
            throw new Exception("PayPal gateway is not active or configured.");
        }

        $mode = trim(env('PAYPAL_MODE', 'sandbox'));

        $config = [
            'mode'    => $mode,
            'sandbox' => [
                'client_id'         => trim($this->gateway->client_id_or_key),
                'client_secret'     => trim($this->gateway->client_secret),
                'app_id'            => trim($this->gateway->app_id ?? '') ?: 'APP-80W284485P519543T',
            ],
            'live' => [
                'client_id'         => trim($this->gateway->client_id_or_key),
                'client_secret'     => trim($this->gateway->client_secret),
                'app_id'            => trim($this->gateway->app_id ?? '') ?: 'APP-80W284485P519543T',
            ],
            'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
            'currency'       => 'USD',
            'notify_url'     => '', // Change this accordingly for your application.
            'locale'         => 'en_US', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
            'validate_ssl'   => true, // Validate SSL when creating api client.
        ];

        // Set global config for the package to use
        // This is necessary because some versions of the package might check config('paypal') directly
        // during validation or inside setApiCredentials methods.
        Config::set('paypal', $config);

        $provider = new PayPalClient;
        $provider->setApiCredentials($config);
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        return $provider;
    }

    public function createOrder($amount, $currency = 'USD', $returnUrl, $cancelUrl)
    {
        $provider = $this->getClient();

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $amount
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => $returnUrl,
                "cancel_url" => $cancelUrl
            ]
        ]);

        return $order;
    }

    public function capturePayment($orderId)
    {
        $provider = $this->getClient();
        $response = $provider->capturePaymentOrder($orderId);
        return $response;
    }
}
