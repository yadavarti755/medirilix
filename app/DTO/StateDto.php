<?php

namespace App\DTO;

class StateDto
{
    public $country_id;
    public $name;
    public $iso2;
    public $created_by;
    public $updated_by;

    public function __construct(
        $country_id,
        $name,
        $iso2 = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->country_id = $country_id;
        $this->name       = $name;
        $this->iso2       = $iso2;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
