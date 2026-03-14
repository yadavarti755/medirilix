<?php

namespace App\DTO;

class CountryDto
{
    public  $name;
    public  $iso2;
    public $phone_code;
    public $currency;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $iso2,
        $phone_code = null,
        $currency = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->name       = $name;
        $this->iso2       = $iso2;
        $this->phone_code = $phone_code;
        $this->currency   = $currency;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
