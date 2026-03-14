<?php

namespace App\DTO;

class ReturnPolicyDto
{
    public $title;
    public $return_till_days;
    public $return_description;
    public $created_by;
    public $updated_by;

    public function __construct(
        $title,
        $return_till_days,
        $return_description,
        $created_by = null,
        $updated_by = null
    ) {
        $this->title = $title;
        $this->return_till_days = $return_till_days;
        $this->return_description = $return_description;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
