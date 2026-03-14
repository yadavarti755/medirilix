<?php

namespace App\DTO;

class CouponDto
{
    public $code;
    public $description;
    public $discount_type;
    public $value;
    public $min_spend;
    public $max_discount;
    public $usage_limit_per_coupon;
    public $usage_limit_per_user;
    public $start_date;
    public $end_date;
    public $is_active;
    public $created_by;
    public $product_ids;
    public $category_ids;

    public function __construct(
        $code,
        $description,
        $discount_type,
        $value,
        $min_spend = null,
        $max_discount = null,
        $usage_limit_per_coupon = null,
        $usage_limit_per_user = null,
        $start_date = null,
        $end_date = null,
        $is_active = true,
        $product_ids = [],
        $category_ids = [],
        $created_by = null,
        $updated_by = null
    ) {
        $this->code = $code;
        $this->description = $description;
        $this->discount_type = $discount_type;
        $this->value = $value;
        $this->min_spend = $min_spend;
        $this->max_discount = $max_discount;
        $this->usage_limit_per_coupon = $usage_limit_per_coupon;
        $this->usage_limit_per_user = $usage_limit_per_user;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->is_active = $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->product_ids = $product_ids;
        $this->category_ids = $category_ids;
        $this->product_ids = $product_ids;
        $this->category_ids = $category_ids;
    }
}
