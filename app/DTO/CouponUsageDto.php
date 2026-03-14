<?php

namespace App\DTO;

class CouponUsageDto
{
    public function __construct(
        public int $coupon_id,
        public int $user_id,
        public int $order_id,
    ) {}
}
