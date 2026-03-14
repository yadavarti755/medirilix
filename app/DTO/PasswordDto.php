<?php

namespace App\DTO;

class PasswordDto
{
    public $password;
    public function __construct($password)
    {
        $this->password = $password;
    }
}
