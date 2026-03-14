<?php

namespace App\DTO;

class SizeDto
{
    public $name;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $created_by = null,
        $updated_by = null
    ) {
        $this->name = $name;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
