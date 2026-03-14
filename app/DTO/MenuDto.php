<?php

namespace App\DTO;

class MenuDto
{
    public $title;
    public $url;
    public $parent_id;
    public $order;
    public $location;
    public $permission_name;
    public $created_by;
    public $updated_by;

    public function __construct(
        $title,
        $url,
        $parent_id,
        $order,
        $location,
        $permission_name,
        $created_by,
        $updated_by
    ) {
        $this->title = $title;
        $this->url = $url;
        $this->parent_id = $parent_id;
        $this->order = $order;
        $this->location = $location;
        $this->permission_name = $permission_name;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
