<?php

namespace App\DTO;

class PageDto
{
    public ?int $menu_id;
    public string $title;
    public ?string $content;
    public $is_published;
    public $created_by;
    public $updated_by;

    public function __construct(
        ?int $menu_id,
        string $title,
        ?string $content = null,
        $is_published = 0,
        $created_by,
        $updated_by = null
    ) {
        $this->menu_id = $menu_id;
        $this->title = $title;
        $this->content = $content;
        $this->is_published = $is_published;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
