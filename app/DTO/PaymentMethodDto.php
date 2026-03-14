<?php

namespace App\DTO;

class PaymentMethodDto
{
    public $title;
    public $image;
    public $is_published;
    public $created_by;
    public $updated_by;

    public function __construct(
        $title = null,
        $image = null,
        $is_published = 0,
        $created_by = null,
        $updated_by = null
    ) {
        $this->title = $title;
        $this->image = $image;
        $this->is_published = $is_published;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
