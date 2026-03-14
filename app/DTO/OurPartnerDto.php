<?php

namespace App\DTO;

class OurPartnerDto
{
    public $file_name;
    public $title;
    public $link;
    public $created_by;
    public $updated_by;

    public function __construct(
        $file_name = null,
        $title,
        $link,
        $created_by,
        $updated_by = null
    ) {
        $this->file_name = $file_name;
        $this->title = $title;
        $this->link = $link;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
