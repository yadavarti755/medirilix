<?php

namespace App\Services;

use App\DTO\CouponDto;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;

class CouponService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new \App\Repositories\CouponRepository();
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findByCode($code)
    {
        return $this->repository->findByCode($code);
    }

    public function create(CouponDto $dto)
    {
        try {
            $data = [
                'code' => $dto->code,
                'description' => $dto->description,
                'discount_type' => $dto->discount_type,
                'value' => $dto->value,
                'min_spend' => $dto->min_spend,
                'max_discount' => $dto->max_discount,
                'usage_limit_per_coupon' => $dto->usage_limit_per_coupon,
                'usage_limit_per_user' => $dto->usage_limit_per_user,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'is_active' => $dto->is_active,
                'created_by' => $dto->created_by,
                'updated_by' => $dto->updated_by,
                'product_ids' => $dto->product_ids,
                'category_ids' => $dto->category_ids,
            ];
            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('Coupon creation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function update(CouponDto $dto, $id)
    {
        try {
            $data = [
                'code' => $dto->code,
                'description' => $dto->description,
                'discount_type' => $dto->discount_type,
                'value' => $dto->value,
                'min_spend' => $dto->min_spend,
                'max_discount' => $dto->max_discount,
                'usage_limit_per_coupon' => $dto->usage_limit_per_coupon,
                'usage_limit_per_user' => $dto->usage_limit_per_user,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'is_active' => $dto->is_active,
                'updated_by' => $dto->updated_by,
                'product_ids' => $dto->product_ids,
                'category_ids' => $dto->category_ids,
            ];

            return $this->repository->update($data, $id);
        } catch (\Exception $e) {
            Log::error('Coupon update failed: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('Coupon deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
