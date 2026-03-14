<?php

namespace App\DTO;

class MediaDto
{
    public $file_name;
    public $original_name;
    public $mime_type;
    public $size;
    public ?string $alt_text;
    public $created_by;
    public $updated_by;
    public $is_approved;
    public $is_published;
    public $remarks;

    public function __construct(
        $file_name,
        $original_name = null,
        $mime_type = null,
        $size = null,
        ?string $alt_text = null,
        $created_by,
        $updated_by = null,
        $is_approved = 0,
        $is_published = 0,
        $remarks = null
    ) {
        $this->file_name = $file_name;
        $this->original_name = $original_name;
        $this->mime_type = $mime_type;
        $this->size = $size;
        $this->alt_text = $alt_text;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->is_approved = $is_approved;
        $this->is_published = $is_published;
        $this->remarks = $remarks;
    }
}
