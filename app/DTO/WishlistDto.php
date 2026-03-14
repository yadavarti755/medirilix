<?php

namespace App\DTO;

class WishlistDto
{
    public $user_id;
    public $product_id;
    public $created_by;
    public $updated_by;

    public function __construct(
        $user_id,
        $product_id,
        $created_by = null,
        $updated_by = null
    ) {
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
}
