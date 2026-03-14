<?php

namespace App\DTO;

class UserDto
{
    public $name;
    public $email;
    public $mobile_number;
    public $password;
    public $roles;
    // public array $permissions = [];
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $email,
        $mobile_number,
        $password = '',
        $roles,
        // $permissions,
        $created_by,
        $updated_by = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->mobile_number = $mobile_number;
        $this->password = $password;
        $this->roles = $roles;
        // $this->permissions = $permissions;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
