<?php

namespace App\DTO;

class ContactUsDto
{
    public $name;
    public $email_id;
    public $phone_number;
    public $message;
    public $status;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $email_id,
        $phone_number,
        $message,
        $status = '0',
        $created_by = null,
        $updated_by = null
    ) {
        $this->name = $name;
        $this->email_id = $email_id;
        $this->phone_number = $phone_number;
        $this->message = $message;
        $this->status = $status;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
