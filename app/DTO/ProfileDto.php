<?php

namespace App\DTO;

class ProfileDto
{
    public $name;
    public $email;
    public $mobile_number;
    public $profile_image;

    public function __construct($name, $email = '', $mobile_number = '', $profile_image = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->mobile_number = $mobile_number;
        $this->profile_image = $profile_image;
    }
}
