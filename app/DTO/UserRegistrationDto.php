<?php

namespace App\DTO;

class UserRegistrationDto
{
    public $user_id;
    public $name;
    public $username;
    public $email;
    public $phone_number;
    public $password;
    public $roles;
    public $is_verified;
    public $created_by;

    public function __construct(
        $name,
        $email,
        $phone_number,
        $password,
        $roles = ['USER'],
        $is_verified = 0,
        $user_id = null,
        $created_by = 0
    ) {
        $this->user_id = $user_id ?? sha1(time() . $email);
        $this->name = $name;
        $this->username = $email;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->password = $password;
        $this->roles = $roles;
        $this->is_verified = $is_verified;
        $this->created_by = $created_by;
    }
}
