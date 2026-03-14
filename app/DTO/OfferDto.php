<?php

namespace App\DTO;

class OfferDto
{
    public $title;
    public $description;
    public $image;
    public $type;
    public $type_id;
    public $is_active;
    public $created_by;
    public $updated_by;

    public function __construct(
        $title,
        $description,
        $image,
        $type,
        $type_id,
        $is_active,
        $created_by = null,
        $updated_by = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->type = $type;
        $this->type_id = $type_id;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
