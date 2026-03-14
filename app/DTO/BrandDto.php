<?php

namespace App\DTO;

class BrandDto
{
    public $name;
    public $file_name;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $file_name = null,
        $created_by = null,
        $updated_by = null
    ) {
        $this->name = $name;
        $this->file_name = $file_name;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
