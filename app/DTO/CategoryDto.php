<?php

namespace App\DTO;

class CategoryDto
{
    public $name;
    public $description;
    public $image;
    public $parent_id;
    public $order;
    public $is_published;
    public $created_by;
    public $updated_by;

    public function __construct(
        $name,
        $description = null,
        $image = null,
        $parent_id = null,
        $order = null,
        $is_published = 1,
        $created_by,
        $updated_by = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->parent_id = $parent_id;
        $this->order = $order;
        $this->is_published = $is_published;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
