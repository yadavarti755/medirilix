<?php

namespace App\DTO;

class PaymentGatewayDto
{
    public $gateway_name;
    public $app_id;
    public $client_id_or_key;
    public $client_secret;
    public $image;
    public $is_active;
    public $created_by;
    public $updated_by;

    public function __construct(
        $gateway_name,
        $app_id,
        $client_id_or_key,
        $client_secret,
        $image = null,
        $is_active = 1,
        $created_by = null,
        $updated_by = null
    ) {
        $this->gateway_name = $gateway_name;
        $this->app_id = $app_id;
        $this->client_id_or_key = $client_id_or_key;
        $this->client_secret = $client_secret;
        $this->image = $image;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
