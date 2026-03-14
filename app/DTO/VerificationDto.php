<?php

namespace App\DTO;

class VerificationDto
{
    public $email_id;
    public $verification_code;
    public $verify_type;

    public function __construct(
        $email_id,
        $verification_code,
        $verify_type
    ) {
        $this->email_id = $email_id;
        $this->verification_code = $verification_code;
        $this->verify_type = $verify_type;
    }
}
