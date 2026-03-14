<?php

namespace App\DTO;

class PaymentDto
{
    // Placeholder DTO if specific transfer objects are defined later.
    // For now, Payment logic relies heavily on associative arrays for Gateway payloads.
    // We can define properties if we standardize the gateway request.

    public $txnId;
    public $amount;
    public $productInfo;
    public $firstName;
    public $email;
    public $phone;
    public $udf1; // Usually Order Number
    public $surl;
    public $furl;
    public $key;
    public $hash;

    // Constructor to potentially initialize standard fields
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
