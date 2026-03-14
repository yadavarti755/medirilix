<?php

namespace App\DTO;

class ReturnReasonDto
{
    public $title;
    public $created_by;
    public $updated_by;

    public function __construct(
        $title,
        $created_by = null,
        $updated_by = null
    ) {
        $this->title = $title;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
