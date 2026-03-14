<?php

namespace App\DTO;

class ContactDetailDto
{
    public $address;
    public $phone_numbers;
    public $email_ids;
    public $is_primary;
    public $created_by;
    public $updated_by;

    public function __construct(
        $address,
        $phone_numbers = null,
        $email_ids = null,
        $is_primary = 0,
        $created_by,
        $updated_by = null
    ) {
        $this->address = $address;
        $this->phone_numbers = $phone_numbers;
        $this->email_ids = $email_ids;
        $this->is_primary = $is_primary;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
