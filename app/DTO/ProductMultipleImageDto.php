<?php

namespace App\DTO;

class ProductMultipleImageDto
{
    public $product_id;
    public $image_name;
    public $created_by;
    public $updated_by;

    public function __construct(
        $product_id = null,
        $image_name = 'no-image.png',
        $created_by = null,
        $updated_by = null,
    ) {
        $this->product_id = $product_id;
        $this->image_name = $image_name;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
