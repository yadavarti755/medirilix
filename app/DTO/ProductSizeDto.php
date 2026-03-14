<?php

namespace App\DTO;

class ProductSizeDto
{
    public $product_id;
    public $size_id;

    public function __construct(
        $product_id,
        $size_id
    ) {
        $this->product_id = $product_id;
        $this->size_id = $size_id;
    }
}
