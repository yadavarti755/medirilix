<?php

namespace App\DTO;

class RoleDto
{
    public $name;
    public $landing_page_url;
    public $permissions;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $landing_page_url,
        $permissions,
        $created_by,
        $updated_by
    ) {
        $this->name = $name;
        $this->landing_page_url = $landing_page_url;
        $this->permissions = $permissions;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
