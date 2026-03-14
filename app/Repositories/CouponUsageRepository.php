<?php

namespace App\Repositories;

use App\Models\CouponUsage;

class CouponUsageRepository
{
    public function create(array $data)
    {
        return CouponUsage::create($data);
    }

    public function countGlobalUsage($couponId)
    {
        return CouponUsage::where('coupon_id', $couponId)->count();
    }

    public function countUserUsage($couponId, $userId)
    {
        return CouponUsage::where('coupon_id', $couponId)
            ->where('user_id', $userId)
            ->count();
    }

    public function deleteByOrderId($orderId)
    {
        return CouponUsage::where('order_id', $orderId)->delete();
    }
}
