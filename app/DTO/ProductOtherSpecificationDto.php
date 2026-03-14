<?php

namespace App\DTO;

class ProductOtherSpecificationDto
{
    public $product_id;
    public $label;
    public $value;
    public $created_by;
    public $updated_by;

    public function __construct(
        $product_id,
        $label,
        $value,
        $created_by = null,
        $updated_by = null
    ) {
        $this->product_id = $product_id;
        $this->label = $label;
        $this->value = $value;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
