<?php

namespace App\DTO;

class ReturnReasonsListDto
{
    public $code;
    public $text;

    public function __construct(
        $code,
        $text
    ) {
        $this->code = $code;
        $this->text = $text;
    }
}
