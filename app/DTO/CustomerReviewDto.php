<?php

namespace App\DTO;

class CustomerReviewDto
{
    public function __construct(
        public $user_id,
        public $product_id,
        public $message,
        public $rating,
        public $images = [],
        public $created_by = null,
        public $updated_by = null,
    ) {}
}
