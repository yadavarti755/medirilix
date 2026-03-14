<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initiate the payment process.
     *
     * @param array $paymentData Data required to initiate payment (amount, order_id, user info, etc.)
     * @return array Result containing status, redirect URL, or data for form submission.
     */
    public function initiate(array $paymentData);

    /**
     * Process the callback or response from the gateway.
     *
     * @param array $data Data received from the gateway callback.
     * @return array Result containing status, transaction ID, and other relevant info.
     */
    public function processCallback(array $data);
}
