<?php

namespace App\DTO;

class CurrencyDto
{
    public $currency;
    public $symbol;
    public $amount_in_dollars;
    public $created_by;
    public $updated_by;

    public function __construct(
        $currency,
        $symbol,
        $amount_in_dollars,
        $created_by = null,
        $updated_by = null
    ) {
        $this->currency = $currency;
        $this->symbol = $symbol;
        $this->amount_in_dollars = $amount_in_dollars;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
