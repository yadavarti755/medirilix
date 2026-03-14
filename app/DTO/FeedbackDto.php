<?php

namespace App\DTO;

class FeedbackDto
{
    public $name;
    public $email;
    public $mobile_no;
    public $message;

    public function __construct(
        $name,
        $email,
        $mobile_no = null,
        $message
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->mobile_no = $mobile_no;
        $this->message = $message;
    }
}
