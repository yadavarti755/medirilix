<?php

namespace App\DTO;

class SliderDto
{
    public $category_id;
    public $title;
    public $subtitle;
    public $description;
    public $file_name;
    public $is_published;
    public $created_by;
    public $updated_by;

    public function __construct(
        $category_id = null,
        $title = null,
        $subtitle = null,
        $description = null,
        $file_name = null,
        $is_published = 0,
        $created_by,
        $updated_by = null
    ) {
        $this->category_id = $category_id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->description = $description;
        $this->file_name = $file_name;
        $this->is_published = $is_published;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
