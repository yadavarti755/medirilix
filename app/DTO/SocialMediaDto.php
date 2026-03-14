<?php

namespace App\DTO;

class SocialMediaDto
{
    public $type;
    public $name;
    public $url;
    public $icon_class;
    public $created_by;
    public $updated_by;

    public function __construct(
        $type,
        $name,
        $url,
        $icon_class = null,
        $created_by,
        $updated_by = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->url = $url;
        $this->icon_class = $icon_class;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
