<?php

namespace App\DTO;

class SubscribeNewsletterDto
{
    public $email_id;

    public function __construct($email_id)
    {
        $this->email_id = $email_id;
    }
}
