<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Services\Gateways\PayuGateway;
use App\Services\Gateways\PaypalGateway;
use App\Services\Gateways\PayoneerGateway;
use Exception;

use App\Services\Gateways\StripeGateway;

class PaymentGatewayFactory
{
    public static function create($gatewayCode)
    {
        $gatewayConfig = PaymentGateway::where('code', $gatewayCode)->first();

        if (!$gatewayConfig || !$gatewayConfig->is_active) {
            throw new Exception("Payment gateway '{$gatewayCode}' is not active or found.");
        }

        $credentials = $gatewayConfig->credentials;
        $testMode = $gatewayConfig->test_mode;

        switch ($gatewayCode) {
            case 'payu':
                return new PayuGateway($credentials, $testMode);
            case 'paypal':
                return new PaypalGateway($credentials, $testMode);
            case 'payoneer':
                return new PayoneerGateway($credentials, $testMode);
            case 'stripe':
                return new StripeGateway($credentials, $testMode);
            default:
                throw new Exception("Gateway implementation not found for '{$gatewayCode}'");
        }
    }
}
