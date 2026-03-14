<?php

namespace App\Services;

use App\Repositories\CouponUsageRepository;
use App\DTO\CouponUsageDto;

class CouponUsageService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new CouponUsageRepository();
    }

    public function logUsage(CouponUsageDto $dto)
    {
        return $this->repository->create((array) $dto);
    }

    public function checkGlobalLimit($couponId, $limit)
    {
        if ($limit <= 0) return true;
        return $this->repository->countGlobalUsage($couponId) < $limit;
    }

    public function checkUserLimit($couponId, $userId, $limit)
    {
        if ($limit <= 0) return true;
        return $this->repository->countUserUsage($couponId, $userId) < $limit;
    }

    public function revertUsageForOrder($orderId)
    {
        return $this->repository->deleteByOrderId($orderId);
    }
}
